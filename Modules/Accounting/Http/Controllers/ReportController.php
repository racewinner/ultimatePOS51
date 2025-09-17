<?php

namespace Modules\Accounting\Http\Controllers;

use App\Business;
use Modules\Accounting\Entities\BusinessLocation;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounting\Exports\AccountingExport;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Services\AccountingReportService;
use Modules\Accounting\Entities\AccountSubtype;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\Transaction;
use Modules\Accounting\Services\BudgetService;

class ReportController extends Controller
{
    private $default_start_date;
    private $default_end_date;

    public function __construct()
    {
        $this->default_start_date = date('Y') . '-' . '01-01';
        $this->default_end_date = date('Y-m-d');
    }

    public function index()
    {
        $reports = (new AccountingReportService())->getReports();

        return view('accounting::report.index', compact('reports'));
    }

    public function trial_balance(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $currency_code = currency_code();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $account_types = ChartOfAccount::getAccountTypes();
        $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("business_locations", "journal_entries.location_id", "business_locations.id")->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
        })->when($location_id, function ($query) use ($location_id) {
            $query->where('journal_entries.location_id', $location_id);
        })->where('chart_of_accounts.active', 1)
            ->where('chart_of_accounts.business_id', session('business.id'))
            ->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")
            ->groupBy("chart_of_accounts.id")
            ->get();

        $compact_data = compact(
            'start_date',
            'end_date',
            'location_id',
            'data',
            'business_locations',
            'currency_code',
            'account_types'
        );


        if (!empty($start_date)) {
            //check if we should download
            if ($request->download) {
                $view = view('accounting::report.trial_balance_pdf', $compact_data);
                $file_name = trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ')';

                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.trial_balance_pdf'), $compact_data);
                    return $pdf->download("$file_name.pdf");
                } elseif ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), "$file_name.xlsx");
                } elseif ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), "$file_name.xls");
                } elseif ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), "$file_name.csv");
                }
            }
        }

        return view('accounting::report.trial_balance', $compact_data);
    }

    //cash flow statement
    public function cash_flow(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $account_types = ChartOfACcount::getAccountTypes();

        $data = DB::table("chart_of_accounts")
            ->leftJoin('journal_entries', function ($join) use ($end_date) {
                $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->where('journal_entries.date', '<=', $end_date);
                    });
            })
            ->leftJoin('business_locations', function ($join) use ($location_id) {
                $join->on('journal_entries.location_id', '=', 'business_locations.id')
                    ->when($location_id, function ($query) use ($location_id) {
                        $query->where('journal_entries.location_id', $location_id);
                    });
            })
            ->where('chart_of_accounts.active', 1)
            ->where('chart_of_accounts.business_id', session('business.id'))
            ->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")
            ->groupBy("chart_of_accounts.id")
            ->orderBy('account_type')
            ->get();

        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'account_types');

        //check if we should download
        if ($request->download) {
            $view = view(
                'accounting::report.cash_flow_pdf',
                $compact_data
            );

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.cash_flow_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::general.cash_flow', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.cash_flow', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.cash_flow', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.cash_flow', 1) . '.csv');
            }
        }

        return view(
            'accounting::report.cash_flow',
            $compact_data
        );
    }
    //income statement
    public function profit_and_loss(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $account_types = ['income', 'expense'];
        $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("business_locations", "journal_entries.location_id", "business_locations.id")->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
        })->when($location_id, function ($query) use ($location_id) {
            $query->where('journal_entries.location_id', $location_id);
        })->where('chart_of_accounts.active', 1)->whereIn('chart_of_accounts.account_type', $account_types)
            ->where('chart_of_accounts.business_id', session('business.id'))
            ->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")
            ->groupBy("chart_of_accounts.id")->orderBy('account_type')
            ->get();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $compact_data = compact(
            'start_date',
            'end_date',
            'location_id',
            'data',
            'business_locations',
            'currency_code',
            'account_types'
        );

        if (!empty($start_date)) {
            //check if we should download
            if ($request->download) {
                $view = view('accounting::report.profit_and_loss_pdf', $compact_data);
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.profit_and_loss_pdf'), $compact_data);
                    return $pdf->download(trans_choice('accounting::report.profit_and_loss', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                } elseif ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::report.profit_and_loss', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                } elseif ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::report.profit_and_loss', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                } elseif ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::report.profit_and_loss', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return view('accounting::report.profit_and_loss', $compact_data);
    }

    //balance sheet
    public function balance_sheet(Request $request)
    {
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $account_types = ['asset', 'equity', 'liability'];

        if (!empty($end_date)) {
            $data = DB::table("chart_of_accounts")
                ->leftJoin('journal_entries', function ($join) use ($end_date) {
                    $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                        ->when($end_date, function ($query) use ($end_date) {
                            $query->where('journal_entries.date', '<=', $end_date);
                        });
                })
                ->leftJoin('business_locations', function ($join) use ($location_id) {
                    $join->on('journal_entries.location_id', '=', 'business_locations.id')
                        ->when($location_id, function ($query) use ($location_id) {
                            $query->where('journal_entries.location_id', $location_id);
                        });
                })
                ->leftJoin('account_subtypes', 'account_subtypes.account_type', '=', 'chart_of_accounts.account_type')
                ->where('chart_of_accounts.active', 1)
                ->whereIn('chart_of_accounts.account_type', ['asset', 'equity', 'liability'])
                ->selectRaw("chart_of_accounts.name,
                            chart_of_accounts.gl_code,
                            chart_of_accounts.account_type,
                            account_subtypes.id account_subtype_id,
                            account_subtypes.name account_subtype,
                            business_locations.name business_location,
                            journal_entries.debit,journal_entries.credit")
                ->where('chart_of_accounts.business_id', session('business.id'))
                ->groupBy("chart_of_accounts.id")
                ->orderBy('account_type')
                ->get();

            $compact_data = compact('end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'account_types');

            //check if we should download
            if ($request->download) {
                $view = view('accounting::report.balance_sheet_pdf', $compact_data);
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.balance_sheet_pdf'), $compact_data);
                    return $pdf->download(trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').pdf');
                } elseif ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xlsx');
                } elseif ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xls');
                } elseif ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').csv');
                }
            }
        }

        return view('accounting::report.balance_sheet', $compact_data);
    }

    public function ledger(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $account_types = ChartOfAccount::getAccountTypes();

        $data = DB::table("chart_of_accounts")
            ->leftJoin('journal_entries', function ($join) use ($end_date) {
                $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->where('journal_entries.date', '<=', $end_date);
                    });
            })
            ->leftJoin('business_locations', function ($join) use ($location_id) {
                $join->on('journal_entries.location_id', '=', 'business_locations.id')
                    ->when($location_id, function ($query) use ($location_id) {
                        $query->where('journal_entries.location_id', $location_id);
                    });
            })
            ->leftJoin('account_subtypes', 'account_subtypes.id', '=', 'chart_of_accounts.account_subtype_id')
            ->where('chart_of_accounts.active', 1)
            ->where('chart_of_accounts.business_id', session('business.id'))
            ->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,business_locations.name business_location,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit, account_subtypes.name account_subtype, account_subtypes.id account_subtype_id")
            ->groupBy("chart_of_accounts.id")
            ->orderBy('account_type')
            ->get();

        $account_subtype_ids = $data->pluck('account_subtype_id')->unique();
        $account_subtypes = AccountSubtype::forBusiness()->whereIn('id', $account_subtype_ids)->active()->get();

        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'account_types', 'account_subtypes');
        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.ledger_pdf', $compact_data);

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.ledger_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::general.ledger', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.csv');
            }
        }
        return view('accounting::report.ledger', $compact_data);
    }

    public function accounts_receivable_ageing_summary(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = Transaction::where('transactions.type', 'sell')
            ->where('payment_status', '!=', 'paid')
            ->join('journal_entries', 'journal_entries.id', '=', 'transactions.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entries.chart_of_account_id')
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('transactions.transaction_date', [$start_date, date('Y-m-d', strtotime("$end_date + 1day"))]);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('transactions.location_id', $location_id);
            })
            ->select(
                'transactions.payment_status',
                'transactions.ref_no',
                'transactions.transaction_date',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                'journal_entries.transaction_type',
                'chart_of_accounts.name as chart_of_account',
                'chart_of_accounts.id as chart_of_account_id',
            )
            ->get();

        $chart_of_accounts = $data->pluck('chart_of_account_id', 'chart_of_account')->unique();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $account_subtype_ids = $data->pluck('account_subtype_id')->unique();
        $days_past = get_days_past();
        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'chart_of_accounts', 'days_past');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.accounts_receivable_ageing_summary_pdf', $compact_data);
            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.accounts_receivable_ageing_summary_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::report.accounts_receivable_ageing_summary', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_summary', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_summary', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_summary', 1) . '.csv');
            }
        }

        return view('accounting::report.accounts_receivable_ageing_summary')->with($compact_data);
    }

    public function accounts_receivable_ageing_detail(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = Transaction::where('type', 'sell')
            ->where('payment_status', '!=', 'paid')
            ->join('journal_entries', 'journal_entries.id', '=', 'transactions.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entries.chart_of_account_id')
            ->join('account_subtypes', 'account_subtypes.id', '=', 'chart_of_accounts.account_subtype_id')
            ->join('account_detail_types', 'account_detail_types.id', '=', 'chart_of_accounts.detail_type_id')
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('transactions.location_id', $location_id);
            })
            ->select(
                'transactions.transaction_date',
                'transactions.invoice_no',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                'journal_entries.transaction_type',
                'chart_of_accounts.name as chart_of_account',
                'account_subtypes.id as account_subtype_id',
                'account_subtypes.name as account_subtype',
                'account_detail_types.name as account_detail_type',
            )
            ->get();

        $days_passed_options = Transaction::getDaysPassedOptions();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $chart_of_accounts = ChartOfAccount::forBusiness()->where('active', 1)->orderBy('gl_code')->get();
        $account_subtype_ids = $data->pluck('account_subtype_id')->unique();
        $account_subtypes = AccountSubtype::forBusiness()->whereIn('id', $account_subtype_ids)->active()->get();
        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'chart_of_accounts', 'account_subtypes', 'days_passed_options');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.accounts_receivable_ageing_detail_pdf', $compact_data);

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.accounts_receivable_ageing_detail_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.csv');
            }
        }

        return view('accounting::report.accounts_receivable_ageing_detail')->with($compact_data);
    }

    public function accounts_payable_ageing_summary(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = Transaction::where('transactions.type', 'purchase')
            ->where('payment_status', '!=', 'paid')
            ->join('journal_entries', 'journal_entries.id', '=', 'transactions.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entries.chart_of_account_id')
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('transactions.transaction_date', [$start_date, date('Y-m-d', strtotime("$end_date + 1day"))]);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('transactions.location_id', $location_id);
            })
            ->select(
                'transactions.payment_status',
                'transactions.ref_no',
                'transactions.transaction_date',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                'journal_entries.transaction_type',
                'chart_of_accounts.name as chart_of_account',
                'chart_of_accounts.id as chart_of_account_id',
            )
            ->get();

        $chart_of_accounts = $data->pluck('chart_of_account_id', 'chart_of_account')->unique();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $account_subtype_ids = $data->pluck('account_subtype_id')->unique();
        $days_past = get_days_past();
        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'chart_of_accounts', 'days_past');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.accounts_payable_ageing_summary_pdf', $compact_data);
            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.accounts_payable_ageing_summary_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::report.accounts_payable_ageing_summary', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_payable_ageing_summary', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_payable_ageing_summary', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_payable_ageing_summary', 1) . '.csv');
            }
        }

        return view('accounting::report.accounts_payable_ageing_summary')->with($compact_data);
    }

    public function accounts_payable_ageing_detail(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = Transaction::where('type', 'purchase')
            ->where('payment_status', '!=', 'paid')
            ->join('journal_entries', 'journal_entries.id', '=', 'transactions.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entries.chart_of_account_id')
            ->join('account_subtypes', 'account_subtypes.id', '=', 'chart_of_accounts.account_subtype_id')
            ->join('account_detail_types', 'account_detail_types.id', '=', 'chart_of_accounts.detail_type_id')
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('transactions.location_id', $location_id);
            })
            ->select(
                'transactions.transaction_date',
                'transactions.payment_status',
                'transactions.ref_no',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                'journal_entries.transaction_type',
                'chart_of_accounts.name as chart_of_account',
                'account_subtypes.id as account_subtype_id',
                'account_subtypes.name as account_subtype',
                'account_detail_types.name as account_detail_type',
            )
            ->get();

        $days_passed_options = Transaction::getDaysPassedOptions();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $chart_of_accounts = ChartOfAccount::forBusiness()->where('active', 1)->orderBy('gl_code')->get();
        $account_subtype_ids = $data->pluck('account_subtype_id')->unique();
        $account_subtypes = AccountSubtype::forBusiness()->whereIn('id', $account_subtype_ids)->active()->get();
        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'chart_of_accounts', 'account_subtypes', 'days_passed_options');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.accounts_receivable_ageing_detail_pdf', $compact_data);

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.accounts_receivable_ageing_detail_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.accounts_receivable_ageing_detail', 1) . '.csv');
            }
        }

        return view('accounting::report.accounts_payable_ageing_detail')->with($compact_data);
    }

    public function budget_overview(Request $request)
    {
        $financial_year_start = Business::findOrFail(session('business.id'))->fy_start_month;
        $account_types = ChartOfAccount::forBusiness()->where('active', 1)->distinct('account_type')->get(['account_type'])->pluck('account_type');
        $financial_year = !empty(request()->year) ? request()->year : BudgetService::getCurrentFinancialYear($financial_year_start);
        $months = (new BudgetService($financial_year_start, $financial_year))->getMonths();
        $calendar_year = get_calendar_year();
        $url = (object) [
            'monthly' => url("report/accounting/budget_overview?view=monthly&year=$financial_year"),
            'quarterly' => url("report/accounting/budget_overview?view=quarterly&year=$financial_year"),
        ];
        $filters = [
            'view' => $request->view,
            'year' => $request->year,
        ];

        $page_title = ucfirst($request->view) . ' ' . trans_choice('accounting::general.budget', 1) . ' - ' . $financial_year . ' ' . trans_choice('accounting::general.financial_year', 1);

        $chart_of_accounts_by_type = collect([]);
        foreach ($account_types as $account_type) {
            $chart_of_account = ChartOfAccount::with('budget')->where('active', 1)->where('account_type', $account_type)->get();
            $chart_of_account->yearly_total = $chart_of_account->pluck('budget.yearly')->sum();
            $chart_of_accounts_by_type->put($account_type, $chart_of_account);
        }

        $chart_of_accounts = ChartOfAccount::forBusiness()->with('budget')->where('active', 1)->get();
        $chart_of_accounts->yearly_total = $chart_of_accounts->pluck('budget.yearly')->sum();
        $compact_data = compact('chart_of_accounts_by_type', 'chart_of_accounts', 'months', 'calendar_year', 'financial_year_start', 'financial_year', 'url', 'account_types', 'filters', 'page_title');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.budget.budget_overview_pdf', $compact_data);

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.budget.budget_overview_pdf'), $compact_data);
                return $pdf->download($page_title . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), $page_title . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), $page_title . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), $page_title . '.csv');
            }
        }

        return view('accounting::report.budget.budget_overview')->with($compact_data);
    }

    public function journal(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $currency_code = currency_code();
        $chart_of_account_id = $request->chart_of_account_id;
        $account_types = ChartOfAccount::getAccountTypes();

        $data = JournalEntry::leftJoin("business_locations", "business_locations.id", "journal_entries.location_id")
            ->leftJoin("chart_of_accounts", "chart_of_accounts.id", "journal_entries.chart_of_account_id")
            ->leftJoin("users", "users.id", "journal_entries.created_by_id")
            ->leftJoin("account_subtypes", "account_subtypes.id", "chart_of_accounts.account_subtype_id")
            ->leftJoin("account_detail_types", "account_detail_types.id", "chart_of_accounts.detail_type_id")
            ->when($end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween("journal_entries.date", [$start_date, $end_date]);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("journal_entries.location_id", $location_id);
            })
            ->when($chart_of_account_id, function ($query) use ($chart_of_account_id) {
                $query->where("journal_entries.chart_of_account_id", $chart_of_account_id);
            })
            ->where('business_locations.business_id', session('business.id'))
            ->selectRaw("journal_entries.id,journal_entries.created_by_id,journal_entries.location_id,journal_entries.date,journal_entries.debit,journal_entries.credit,journal_entries.transaction_number,business_locations.name business_location,chart_of_accounts.account_type,chart_of_accounts.name account_name,concat(users.first_name,' ',users.last_name) created_by, account_subtypes.name account_subtype, account_detail_types.name account_detail_type")
            ->get();

        $compact_data = compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'currency_code', 'account_types');

        //check if we should download
        if ($request->download) {
            $view = view('accounting::report.journal_pdf', $compact_data);

            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.journal_pdf'), $compact_data);
                return $pdf->download(trans_choice('accounting::report.journal', 1) . '.pdf');
            } elseif ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.journal', 1) . '.xlsx');
            } elseif ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.journal', 1) . '.xls');
            } elseif ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::report.journal', 1) . '.csv');
            }
        }

        return view('accounting::report.journal', $compact_data);
    }
}
