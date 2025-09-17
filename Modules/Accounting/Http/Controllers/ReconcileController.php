<?php

namespace Modules\Accounting\Http\Controllers;

use App\Business;
use Modules\Accounting\Services\FlashService;
use Modules\Accounting\Entities\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;

class ReconcileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $chart_of_accounts = ChartOfAccount::with('account_subtype')->forBusiness()->orderBy('gl_code')->get();
        return view('accounting::reconcile.index', compact('chart_of_accounts'));
    }

    public function start_reconcile(Request $request)
    {
        $request->validate([
            'opening_balance' => ['required'],
            'ending_balance' => ['required'],
            'ending_date' => ['required'],
            'chart_of_account_id' => ['required'],
        ]);

        $computed_data = $request->only(['opening_balance', 'ending_balance', 'ending_date']);
        $chart_of_account = ChartOfAccount::whereHas('journal_entries', function ($query) use ($request) {
            $query->where('date', '<=', $request->ending_date);
        })
            ->forBusiness()
            ->find($request->chart_of_account_id);

        if (!$chart_of_account) {
            (new FlashService())->onWarning(trans('accounting::general.chart_of_account_has_no_journal_entries', [
                'chart_of_account_name' => $request->chart_of_account_name
            ]));
            return redirect('accounting/reconcile')->withInput();
        }

        $difference = $request->ending_balance - $chart_of_account->current_balance;

        $data = [
            'chart_of_accounts' => ChartOfAccount::with('account_subtype')->orderBy('gl_code')->get(),
            'chart_of_account_id' => $chart_of_account->id,
        ];

        $computed_data['total_debit'] = $chart_of_account->journal_entries_not_reversed->sum('debit');
        $computed_data['no_debit'] = $chart_of_account->journal_entries_not_reversed->where('debit', '!=', null)->count();
        $computed_data['total_credit'] = $chart_of_account->journal_entries_not_reversed->sum('credit');
        $computed_data['no_credit'] = $chart_of_account->journal_entries_not_reversed->where('credit', '!=', null)->count();

        $computed_data['currency_code'] = currency_code();
        $computed_data['chart_of_account'] = $chart_of_account;
        $computed_data['difference'] = $difference;

        return view('accounting::reconcile.start_reconcile', array_merge($computed_data, $data));
    }

    public function store_reconcile(Request $request)
    {
        $request->validate([
            'ending_balance' => ['required'],
            'difference' => ['required'],
            'chart_of_account_id' => ['required'],
        ]);

        try {
            DB::beginTransaction();

            $business = Business::findOrFail(session('business.id'));

            $payment_detail = new PaymentDetail();
            $payment_detail->created_by_id = Auth::id();
            $payment_detail->payment_type_id = 'cash';
            $payment_detail->transaction_type = 'reconcile_entry';
            $payment_detail->cheque_number = null;
            $payment_detail->receipt = null;
            $payment_detail->account_number = null;
            $payment_detail->bank_name = null;
            $payment_detail->routing_code = null;
            $payment_detail->save();

            //Add Journal Entry 
            $transaction_number = get_uniqid();
            $today = date('Y-m-d');
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = $transaction_number;
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->location_id = null;
            $journal_entry->currency_id = $business->currency_id;
            $journal_entry->chart_of_account_id = $request->chart_of_account_id;
            $journal_entry->transaction_type = 'reconcile_entry';
            $journal_entry->date = $today;
            $date = explode('-', $today);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $request->difference >= 0 ?
                $journal_entry->credit = $request->difference :
                $journal_entry->debit = $request->difference;
            $journal_entry->manual_entry = 0;
            $journal_entry->notes = trans('accounting::general.reconcile_note', ['amount' => $request->difference]);
            $journal_entry->save();

            // Update the reconcile opening balance
            $chart_of_account = ChartOfAccount::findOrFail($request->chart_of_account_id);
            $chart_of_account->update([
                'reconcile_opening_balance' => $request->ending_balance
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        (new FlashService())->onSuccess(trans('accounting::general.reconcile_success', ['account' => $chart_of_account->name]));
        return redirect('accounting/reconcile');
    }

    public function undo_reconcile(Request $request)
    {
        $request->validate([
            'chart_of_account_id' => ['required'],
            'last_reconcile_transaction_id' => ['required'],
        ]);

        $chart_of_account = ChartOfAccount::findOrFail($request->chart_of_account_id);

        try {
            $journal_entry_to_reverse = JournalEntry::findOrFail($request->last_reconcile_transaction_id);
            $journal_entry_to_reverse->update([
                'reversed' => 1
            ]);

            // Update the reconcile opening balance to be the current balance 
            $chart_of_account->update([
                'reconcile_opening_balance' => $chart_of_account->current_balance
            ]);
        } catch (\Exception $e) {
            (new FlashService())->onException($e);
            return redirect('accounting/reconcile');
        }

        (new FlashService())->onSuccess(trans('accounting::general.undo_reconcile_success', ['account' => $chart_of_account->name]));
        return redirect('accounting/reconcile');
    }
}
