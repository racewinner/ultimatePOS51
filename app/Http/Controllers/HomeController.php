<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Charts\CommonChart;
use App\Currency;
use App\Media;
use App\Transaction;
use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $restUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type == 'user_customer') {
            return redirect()->action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index']);
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! auth()->user()->can('dashboard.data')) {
            return view('home.index', compact('currencies'));
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        //ensure start date starts from at least 30 days before to get sells last 30 days
        $least_30_days = \Carbon::parse($fy['start'])->subDays(30)->format('Y-m-d');

        //get all sells
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $least_30_days, $fy['end']);

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();

        //Chart for sells last 30 days
        $labels = [];
        $all_sell_values = [];
        $all_sell_values_currency2 = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            $total_sell_on_date = $sells_this_fy->where('date', $date)->sum('total_sells');
            $total_sell_on_date_currency2 = $sells_this_fy->where('date', $date)->sum('total_sells_currency2');

            $all_sell_values[] = (float) ($total_sell_on_date ?? 0);
            $all_sell_values_currency2[] = (float) ($total_sell_on_date_currency2 ?? 0);
        }

        //Group sells by location
        $location_sells = [];
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            $values_currency2 = [];

            foreach ($dates as $date) {
                $total_sell_on_date_location = $sells_this_fy->where('date', $date)->where('location_id', $loc_id)->sum('total_sells');
                $total_sell_on_date_location_currency2 = $sells_this_fy->where('date', $date)->where('location_id', $loc_id)->sum('total_sells_currency2');

                $values[] = (float) ($total_sell_on_date_location ?? 0);
                $values_currency2[] = (float) ($total_sell_on_date_location_currency2 ?? 0);
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
            $location_sells[$loc_id]['values_currency2'] = $values_currency2;
        }

        $sells_chart_1 = new CommonChart;

        $sells_chart_1->labels($labels)
                        ->options($this->__chartOptions(__(
                            'home.total_sells',
                            ['currency' => $currency->code]
                            )));

        if (! empty($location_sells)) {
            foreach ($location_sells as $location_sell) {

                $sells_chart_1->dataset(
                    $location_sell['loc_label'] . $business->currency->symbol, 
                    'line', 
                    $location_sell['values']);

                if($business->second_currency_id > 0) {
                    $sells_chart_1->dataset(
                        $location_sell['loc_label'] . $business->secondCurrency->symbol, 
                        'line', 
                        $location_sell['values_currency2']);
                }
            }
        }

        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations') . $business->currency->symbol, 
                                    'line', 
                                    $all_sell_values);
            $sells_chart_1->dataset(__('report.all_locations') . $business->secondCurrency->symbol, 
                                    'line', 
                                    $all_sell_values_currency2);
        }

        $labels = [];
        $values = [];
        $date = strtotime($fy['start']);
        $last = date('m-Y', strtotime($fy['end']));
        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            $total_sell_in_month_year = $sells_this_fy->where('yearmonth', $month_year)->sum('total_sells');
            $total_sell_in_month_year_currency2 = $sells_this_fy->where('yearmonth', $month_year)->sum('total_sells_currency2');

            $values[] = (float) $total_sell_in_month_year ?? 0;
            $values_currency2[] = (float) $total_sell_in_month_year_currency2 ?? 0;
        } while ($month_year != $last);

        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            $values_data_currency2 = [];
            foreach ($fy_months as $month) {
                $total_sell_in_month_year_location = $sells_this_fy->where('yearmonth', $month)->where('location_id', $loc_id)->sum('total_sells');
                $total_sell_in_month_year_location_currency2 = $sells_this_fy->where('yearmonth', $month)->where('location_id', $loc_id)->sum('total_sells_currency2');

                $values_data[] = (float) $total_sell_in_month_year_location ?? 0;
                $values_data_currency2[] = (float) $total_sell_in_month_year_location_currency2 ?? 0;
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
            $fy_sells_by_location_data[$loc_id]['values_currency2'] = $values_data_currency2;
        }

        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
                    ->options($this->__chartOptions(__(
                        'home.total_sells',
                        ['currency' => $currency->code]
                            )));
        if (! empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'] . $business->currency->symbol, 
                        'line', 
                        $location_sell['values']);

                if($business->second_currency_id > 0) {
                    $sells_chart_2->dataset($location_sell['loc_label'] . $business->secondCurrency->symbol, 
                        'line', 
                        $location_sell['values_currency2']);
                }
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations') . $business->currency->symbol,  
                        'line', 
                        $values);
            $sells_chart_2->dataset(__('report.all_locations')  . $business->secondCurrency->symbol,  
                        'line', 
                        $values_currency2);
        }

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (! empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        return view('home.index', compact('sells_chart_1', 'currencies', 'sells_chart_2', 'widgets', 'all_locations', 'common_settings', 'is_admin'));
    }

    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id);

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id);

            $total_ledger_discount = $this->transactionUtil->getTotalLedgerDiscount($business_id, $start, $end);

            $purchase_details['purchase_due'] = $purchase_details['purchase_due'] - $total_ledger_discount['total_purchase_discount'];

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id
            );

            $total_purchase_inc_tax = $purchase_details['total_purchase_inc_tax'] ?? 0;
            $total_purchase_inc_tax_currency2 = $purchase_details['total_purchase_inc_tax_currency2'] ?? 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'] ?? 0;
            $total_purchase_return_inc_tax_currency2 = $transaction_totals['total_purchase_return_inc_tax_currency2'] ?? 0;

            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase_inc_tax;
            $output['total_purchase_currency2'] = $total_purchase_inc_tax_currency2;

            $output['total_purchase_return'] = $total_purchase_return_inc_tax;
            $output['total_purchase_return_currency2'] = $total_purchase_return_inc_tax_currency2;

            $totalPurchaseReturnPaid = $this->transactionUtil->getTotalPurchaseReturnPaid($business_id, $start, $end, $location_id);
            $output['total_purchase_return_paid'] = $totalPurchaseReturnPaid['total_purchase_return_paid'];
            $output['total_purchase_return_paid_currency2'] = $totalPurchaseReturnPaid['total_purchase_return_paid_currency2'];

            $total_sell_inc_tax = $sell_details['total_sell_inc_tax'] ?? 0;
            $total_sell_inc_tax_currency2 = $sell_details['total_sell_inc_tax_currency2'] ?? 0;

            $total_sell_return_inc_tax = $transaction_totals['total_sell_return_inc_tax'] ?? 0;
            $total_sell_return_inc_tax_currency2 = $transaction_totals['total_sell_return_inc_tax_currency2'] ?? 0;

            $totalSellReturnPaid = $this->transactionUtil->getTotalSellReturnPaid($business_id, $start, $end, $location_id);
            $output['total_sell_return_paid'] = $totalSellReturnPaid['total_sell_return_paid'];
            $output['total_sell_return_paid_currency2'] = $totalSellReturnPaid['total_sell_return_paid_currency2'];

            $output['total_sell'] = $total_sell_inc_tax;
            $output['total_sell_currency2'] = $total_sell_inc_tax_currency2;

            $output['total_sell_return'] = $total_sell_return_inc_tax;
            $output['total_sell_return_currency2'] = $total_sell_return_inc_tax_currency2;

            $output['invoice_due'] = $sell_details['invoice_due'] - $total_ledger_discount['total_sell_discount'];
            $output['invoice_due_currency2'] = $sell_details['invoice_due_currency2'] - $total_ledger_discount['total_sell_discount_currency2'];

            $output['total_expense'] = $transaction_totals['total_expense'];
            $output['total_expense_currency2'] = $transaction_totals['total_expense_currency2'];

            //NET = TOTAL SALES - INVOICE DUE - EXPENSE
            $output['net'] = $output['total_sell'] - $output['invoice_due'] - $output['total_expense'];
            $output['net_currency2'] = $output['total_sell_currency2'] - $output['invoice_due_currency2'] - $output['total_expense_currency2'];

            return $output;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $query = VariationLocationDetails::join(
                'product_variations as pv',
                'variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                    ->join(
                        'variations as v',
                        'variation_location_details.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join(
                        'products as p',
                        'variation_location_details.product_id',
                        '=',
                        'p.id'
                    )
                    ->leftjoin(
                        'business_locations as l',
                        'variation_location_details.location_id',
                        '=',
                        'l.id'
                    )
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    ->where('p.is_inactive', 0)
                    ->whereNull('v.deleted_at')
                    ->whereNotNull('p.alert_quantity')
                    ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('variation_location_details.location_id', request()->input('location_id'));
            }

            $products = $query->select(
                'p.name as product',
                'p.type',
                'p.sku',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku',
                'l.name as location',
                'variation_location_details.qty_available as stock',
                'u.short_name as unit'
            )
                    ->groupBy('variation_location_details.id')
                    ->orderBy('stock', 'asc');

            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product.' ('.$row->sku.')';
                    } else {
                        return $row->product.' - '.$row->product_variation.' - '.$row->variation.' ('.$row->sub_sku.')';
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'.(float) $stock.'</span> '.$row->unit;
                })
                ->removeColumn('sku')
                ->removeColumn('sub_sku')
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business = Business::find($business_id);
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::from("transactions as t")
                    ->join('contacts as c','t.contact_id','=','c.id')
                    ->join('business as b', 'b.id', '=', 't.business_id')
                    ->leftJoin('transaction_payments as tp','t.id','=','tp.transaction_id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'purchase')
                    ->where('t.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(t.pay_term_type = 'days', t.pay_term_number, 30 * t.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('t.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                't.id as id',
                't.currency_id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )->groupBy('t.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) use($business) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    $currency = ($row->currency_id == null || $row->currency_id == $business->currency_id) ? $business->currency : $business->secondCurrency;

                    return '<span>'
                            . Util::format_currency($due, $currency)
                            . '</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->removeColumn('currency_id')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->ref_no.'</a>';
                    }

                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business = Business::find($business_id);
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::from("transactions as t")
                    ->join('contacts as c','t.contact_id','=','c.id')
                    ->leftJoin('transaction_payments as tp','t.id','=','tp.transaction_id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'sell')
                    ->where('t.payment_status', '!=', 'paid')
                    ->whereNotNull('t.pay_term_number')
                    ->whereNotNull('t.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(t.pay_term_type = 'days', t.pay_term_number, 30 * t.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('t.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                't.id as id',
                't.currency_id',
                'c.name as customer',
                'c.supplier_business_name',
                't.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )->groupBy('t.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) use($business) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    $currency = ($row->currency_id == null || $row->currency_id == $business->currency_id) ? $business->currency : $business->secondCurrency;

                    return  '<span>'
                            . Util::format_currency($due, $currency)
                            . '</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->invoice_no.'</a>';
                    }

                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('currency_id')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    public function loadMoreNotifications()
    {
        // $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);
        $user_id = auth()->user()->id;
        $notifications = DatabaseNotification::select('*')->whereJsonContains('data->receivers', $user_id)
            ->orWhereJsonContains('data->receivers', (string)$user_id)
            ->orderBy('updated_at', 'desc');
        foreach ($notifications->get() as $notification) {
            $read_array = json_decode($notification->read_array);
            if (!is_array($read_array)) {
                $read_array = [];
            }
            $read_array[(string)$user_id] = date('Y-m-d H:i:s');
            $notification->read_array = json_encode($read_array);
            $notification->save();
        }
        $notifications = $notifications->paginate(10);
        // if (request()->input('page') == 1) {
        //     auth()->user()->unreadNotifications->markAsRead();
        // }

        $notifications_data = $this->commonUtil->parseNotifications($notifications);
        return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $user_id = auth()->user()->id;
        $unread_notifications = DatabaseNotification::select('*')
            ->where(function ($query) use ($user_id) {
                $query->whereJsonContains('data->receivers', $user_id)
                ->orWhereJsonContains('data->receivers', (string)$user_id);
            })
            ->where(function ($query) use ($user_id) {
                $query->whereRaw("json_extract(read_array, '$.\"$user_id\"') IS NULL")
                    ->orWhereRaw("json_extract(read_array, '$.\"$user_id\"') = ''");
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        // $all_notifications = $all_notifications->get();
        if ($unread_notifications)
            $total_unread = count($unread_notifications);
        else
            $total_unread = 0;

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
            }
        }
        if (! empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html,
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title,
                ],
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical',
                'padding' => 20,
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && ! empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => ! empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF',
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }

            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }

            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF',
            ],
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }

        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;
        
        $read_array = json_decode($notification->read_array);
        if (!is_array($read_array)) {
            $read_array = [];
        }
        $read_array[(string)$user_id] = date('Y-m-d H:i:s');
        $notification->read_array = json_encode($read_array);
        $notification->save();

        // $notification->markAsRead();

        return view('home.notification_modal')->with([
            'notifications' => [$notification],
        ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {
        if ($request->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getUserLocation($latlng)
    {
        $latlng_array = explode(',', $latlng);

        $response = $this->moduleUtil->getLocationFromCoordinates($latlng_array[0], $latlng_array[1]);

        return ['address' => $response];
    }
}
