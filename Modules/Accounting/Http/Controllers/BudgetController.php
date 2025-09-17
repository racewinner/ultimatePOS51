<?php

namespace Modules\Accounting\Http\Controllers;

use App\Business;
use Modules\Accounting\Services\FlashService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Accounting\Entities\Budget;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\BudgetService;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $financial_year_start = Business::findOrFail(session('business.id'))->fy_start_month;
        $chart_of_accounts = ChartOfAccount::forBusiness()->with('budget')->where('active', 1)->get();
        $financial_year = !empty(request()->year) ? request()->year : BudgetService::getCurrentFinancialYear($financial_year_start);
        $months = (new BudgetService($financial_year_start, $financial_year))->getMonths();
        $calendar_year = get_calendar_year();
        $url = (object)[
            'monthly' => url("accounting/budget?view=monthly&year=$financial_year"),
            'quarterly' => url("accounting/budget?view=quarterly&year=$financial_year"),
            'yearly' => url("accounting/budget?view=yearly&year=$financial_year"),
        ];

        return view('accounting::budget.index', compact('chart_of_accounts', 'months', 'calendar_year', 'financial_year_start', 'financial_year', 'url'));
    }

    public function store_financial_year_start(Request $request)
    {
        $request->validate([
            'financial_year_start' => ['required']
        ]);

        try {
            $accounting_settings = Business::updateOrCreate(
                ['id' => session('business.id')],
                ['fy_start_month' => $request->financial_year_start]
            );

            activity()
                ->on($accounting_settings)
                ->withProperties(['id' => $accounting_settings->id])
                ->log('Update financial year start');
        } catch (\Exception $e) {
            throw $e;
            return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        (new FlashService())->onSave();
        return back();
    }

    /**
     * Update or create the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update_monthly_budget(Request $request)
    {
        $request->validate([
            'chart_of_account_id' => ['required'],
            'business_id' => ['required'],
            'financial_year' => ['required'],
            'month_1' => ['required', 'numeric'],
            'month_2' => ['required', 'numeric'],
            'month_3' => ['required', 'numeric'],
            'month_4' => ['required', 'numeric'],
            'month_5' => ['required', 'numeric'],
            'month_6' => ['required', 'numeric'],
            'month_7' => ['required', 'numeric'],
            'month_8' => ['required', 'numeric'],
            'month_9' => ['required', 'numeric'],
            'month_10' => ['required', 'numeric'],
            'month_11' => ['required'], 'numeric',
            'month_12' => ['required', 'numeric'],
        ]);

        try {
            $budget = Budget::updateOrCreate(
                [
                    'business_id' => session('business.id'),
                    'chart_of_account_id' => $request->chart_of_account_id,
                    'financial_year' => $request->financial_year
                ],
                request()->only(
                    'financial_year',
                    'month_1',
                    'month_2',
                    'month_3',
                    'month_4',
                    'month_5',
                    'month_6',
                    'month_7',
                    'month_8',
                    'month_9',
                    'month_10',
                    'month_11',
                    'month_12'
                )
            );

            activity()
                ->on($budget)
                ->withProperties(['id' => $budget->id])
                ->log('Update Budget');
        } catch (\Exception $e) {
            return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        (new FlashService())->onSave();
        return back();
    }


    public function update_quarterly_budget(Request $request)
    {
        $request->validate([
            'chart_of_account_id' => ['required'],
            'business_id' => ['required'],
            'financial_year' => ['required'],
            'quarter_1' => ['required', 'numeric'],
            'quarter_2' => ['required', 'numeric'],
            'quarter_3' => ['required', 'numeric'],
            'quarter_4' => ['required', 'numeric'],
        ]);

        $quarters = collect($request->only(['quarter_1', 'quarter_2', 'quarter_3', 'quarter_4']));

        $collection = collect(['financial_year' => $request->financial_year]);

        $eliminate_decimals = $request->has('eliminate_decimals');

        $monthly_budget = [];

        foreach ($quarters as $key => $quarter_budget) {

            switch ($key) {
                case 'quarter_1':
                    $monthly_budget['quarter_1'] = (new BudgetService())->quartelyBudgetToMonthly(['month_1', 'month_2', 'month_3'], $quarter_budget, $eliminate_decimals);
                    break;

                case 'quarter_2':
                    $monthly_budget['quarter_2'] = (new BudgetService())->quartelyBudgetToMonthly(['month_4', 'month_5', 'month_6'], $quarter_budget, $eliminate_decimals);
                    break;

                case 'quarter_3':
                    $monthly_budget['quarter_3'] = (new BudgetService())->quartelyBudgetToMonthly(['month_7', 'month_8', 'month_9'], $quarter_budget, $eliminate_decimals);
                    break;

                case 'quarter_4':
                    $monthly_budget['quarter_4'] = (new BudgetService())->quartelyBudgetToMonthly(['month_10', 'month_11', 'month_12'], $quarter_budget, $eliminate_decimals);
                    break;

                default:
                    throw new Exception('Quarter not found');
                    break;
            }
        }

        $merged_monthly_budgets = $collection
            ->merge($monthly_budget['quarter_1'])
            ->merge($monthly_budget['quarter_2'])
            ->merge($monthly_budget['quarter_3'])
            ->merge($monthly_budget['quarter_4']);

        try {
            $budget = Budget::updateOrCreate(
                [
                    'business_id' => session('business.id'),
                    'chart_of_account_id' => $request->chart_of_account_id,
                    'financial_year' => $request->financial_year
                ],
                $merged_monthly_budgets->toArray()
            );

            activity()
                ->on($budget)
                ->withProperties(['id' => $budget->id])
                ->log('Update Budget');
        } catch (\Exception $e) {
            return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        (new FlashService())->onSave();
        return back();
    }

    public function update_yearly_budget(Request $request)
    {
        $request->validate([
            'chart_of_account_id' => ['required'],
            'business_id' => ['required'],
            'financial_year' => ['required'],
            'yearly_budget' => ['required', 'numeric'],
        ]);

        $eliminate_decimals = $request->has('eliminate_decimals');

        $monthly_budgets = (new BudgetService())->yearlyBudgetToMonthly($request->yearly_budget, $eliminate_decimals);

        $monthly_budgets['financial_year'] = $request->financial_year;

        try {
            $budget = Budget::updateOrCreate(
                [
                    'business_id' => session('business.id'),
                    'chart_of_account_id' => $request->chart_of_account_id,
                    'financial_year' => $request->financial_year
                ],
                $monthly_budgets
            );

            activity()
                ->on($budget)
                ->withProperties(['id' => $budget->id])
                ->log('Update Budget');
        } catch (\Exception $e) {
            return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        (new FlashService())->onSave();
        return back();
    }
}
