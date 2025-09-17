<?php

namespace Modules\Accounting\Http\Controllers;

use Modules\Accounting\Entities\BusinessLocation;
use Modules\Accounting\Entities\Currency;
use Modules\Accounting\Services\ApiService;
use Modules\Accounting\Entities\PaymentDetail;
use App\Utils\Util;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounting\Exports\AccountingExport;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\PaymentType;
use Modules\Accounting\Entities\Transfer;
use Yajra\DataTables\Facades\DataTables;

class AccountingController extends Controller
{
    private $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    public function trial_balance(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("business_locations", "journal_entries.location_id", "business_locations.id")->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
            })->when($location_id, function ($query) use ($location_id) {
                $query->where('journal_entries.location_id', $location_id);
            })->where('chart_of_accounts.active', 1)->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView('accounting::report.trial_balance_pdf', compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations'
                    ));
                    return $pdf->download(trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = view(
                    'accounting::report.trial_balance_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return view(
            'accounting::report.trial_balance',
            compact(
                'start_date',
                'end_date',
                'location_id',
                'data',
                'business_locations',
            )
        );
    }

    //income statement
    public function income_statement(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("business_locations", "journal_entries.location_id", "business_locations.id")->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
            })->when($location_id, function ($query) use ($location_id) {
                $query->where('journal_entries.location_id', $location_id);
            })->where('chart_of_accounts.active', 1)->whereIn('chart_of_accounts.account_type', ['income', 'expense'])->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->orderBy('account_type')->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView('accounting::report.income_statement_pdf', compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations'
                    ));
                    return $pdf->download(trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = view(
                    'accounting::report.income_statement_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return view(
            'accounting::report.income_statement',
            compact(
                'start_date',
                'end_date',
                'location_id',
                'data',
                'business_locations',
            )
        );
    }

    //balance sheet
    public function balance_sheet(Request $request)
    {
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($end_date)) {
            $data = DB::table("chart_of_accounts")->leftJoin('journal_entries', function ($join) use ($end_date) {
                $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->where('journal_entries.date', '<=', $end_date);
                    });
            })->leftJoin('business_locations', function ($join) use ($location_id) {
                $join->on('journal_entries.location_id', '=', 'business_locations.id')
                    ->when($location_id, function ($query) use ($location_id) {
                        $query->where('journal_entries.location_id', $location_id);
                    });
            })->where('chart_of_accounts.active', 1)->whereIn('chart_of_accounts.account_type', ['asset', 'equity', 'liability'])->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->orderBy('account_type')->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView('accounting::report.balance_sheet_pdf', compact('end_date', 'location_id', 'data', 'business_locations'));
                    return $pdf->download(trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').pdf');
                }
                $view = view(
                    'accounting::report.balance_sheet_pdf',
                    compact('end_date', 'location_id', 'data', 'business_locations')
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').csv');
                }
            }
        }
        return view(
            'accounting::report.balance_sheet',
            compact('end_date', 'location_id', 'data', 'business_locations')
        );
    }

    public function transfers()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $transfers = Transfer::with('transfer_from')
            ->with('transfer_to')
            ->with('transfer_by')
            ->forBusiness()
            ->get();

        $business_id = session()->get('user.business_id');

        if (request()->ajax()) {
            return DataTables::of($transfers)
                ->editColumn('journal_entry', function ($row) {
                    $journal_transaction_number = $row->journal_transaction_number;
                    return '<a href="/accounting/journal_entry/' . $journal_transaction_number . '/show"> ' . $journal_transaction_number . ' </a>&nbsp;';
                })
                ->editColumn('transfer_from', function ($row) {
                    $transfer_from = $row->transfer_from->name;
                    $transfer_from_id = $row->transfer_from_id;
                    return '<a href="/accounting/chart_of_account/' . $transfer_from_id . '/show"> ' . $transfer_from . ' </a>&nbsp;';
                })
                ->editColumn('transfer_to', function ($row) {
                    $transfer_to = $row->transfer_to->name;
                    $transfer_to_id = $row->transfer_to_id;
                    return '<a href="/accounting/chart_of_account/' . $transfer_to_id . '/show"> ' . $transfer_to . ' </a>&nbsp;';
                })
                ->editColumn('transfer_by', function ($row) {
                    $transfer_by = $row->transfer_by->user_full_name;
                    $transfer_by_id = $row->transfer_by_id;
                    return '<a href="' . action('ManageUserController@show', [$transfer_by_id]) . '"> ' . $transfer_by . ' </a>&nbsp;';
                })
                ->editColumn('arrow_right', function () {
                    return '<i class="fa fa-arrow-right" aria-hidden="true"></i>';
                })
                ->rawColumns(['journal_entry', 'transfer_from', 'transfer_to', 'transfer_by', 'arrow_right'])
                ->make(true);
        }

        return view('accounting::transfers.index')->with(compact('business_id'));
    }

    public function create_transfer()
    {
        $chart_of_accounts = ChartOfAccount::forBusiness()->where('active', 1)->orderBy('gl_code')->get();
        $currencies = Currency::all();
        $payment_types = PaymentType::getTypesCollection();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));

        return view('accounting::transfers.create', compact('chart_of_accounts', 'currencies', 'payment_types', 'business_locations'));
    }

    public function store_transfer(Request $request)
    {
        $request->validate([
            'location_id' => ['required'],
            'currency_id' => ['required'],
            'amount' => ['required'],
            'debit' => ['required'],
            'credit' => ['required'],
            'date' => ['required', 'date']
        ]);

        try {
            DB::beginTransaction();

            $transaction_number = $this->store_transfer_journal_entry($request);

            $transfer = Transfer::create([
                'journal_transaction_number' => $transaction_number,
                'transfer_from_id' => $request->debit,
                'transfer_to_id' => $request->credit,
                'transfer_by_id' => Auth::id(),
                'amount' => $request->amount,
            ]);

            activity()
                ->on($transfer)
                ->withProperties(['id' => $transfer->id])
                ->log('Create Transfer');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new ApiService())->onException($e);
        }

        return (new ApiService())->onSave();
    }

    private function store_transfer_journal_entry(Request $request)
    {
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'journal_transfer_entry';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();

        $transaction_number = get_uniqid();

        //debit account
        $journal_entry = new JournalEntry();
        $journal_entry->created_by_id = Auth::id();
        $journal_entry->payment_detail_id = $payment_detail->id;
        $journal_entry->transaction_number = $transaction_number;
        $journal_entry->location_id = $request->location_id;
        $journal_entry->currency_id = $request->currency_id;
        $journal_entry->chart_of_account_id = $request->debit;
        $journal_entry->transaction_type = 'transfer_entry';
        $journal_entry->date = $request->date;
        $date = explode('-', $request->date);
        $journal_entry->month = $date[1];
        $journal_entry->year = $date[0];
        $journal_entry->debit = $request->amount;
        $journal_entry->reference = $request->reference;
        $journal_entry->manual_entry = 0;
        $journal_entry->notes = $request->notes;
        $journal_entry->save();
        //credit account
        $journal_entry = new JournalEntry();
        $journal_entry->created_by_id = Auth::id();
        $journal_entry->transaction_number = $transaction_number;
        $journal_entry->payment_detail_id = $payment_detail->id;
        $journal_entry->location_id = $request->location_id;
        $journal_entry->currency_id = $request->currency_id;
        $journal_entry->chart_of_account_id = $request->credit;
        $journal_entry->transaction_type = 'transfer_entry';
        $journal_entry->date = $request->date;
        $date = explode('-', $request->date);
        $journal_entry->month = $date[1];
        $journal_entry->year = $date[0];
        $journal_entry->credit = $request->amount;
        $journal_entry->reference = $request->reference;
        $journal_entry->manual_entry = 0;
        $journal_entry->notes = $request->notes;
        $journal_entry->save();

        activity()
            ->on($journal_entry)
            ->withProperties(['id' => $journal_entry->id])
            ->log('Create Journal Entry for Transfer');

        return $transaction_number;
    }
}
