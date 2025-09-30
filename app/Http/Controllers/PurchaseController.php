<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\User;
use App\Currency;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use App\Notifications\PurchaseNotification;
use App\Variation;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Events\PurchaseCreatedOrModified;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(
        ProductUtil $productUtil, 
        TransactionUtil $transactionUtil, 
        BusinessUtil $businessUtil, 
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil,
    ) {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create') && ! auth()->user()->can('view_own_purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        if (request()->ajax()) {
            $purchases = $this->transactionUtil->getListPurchases($business_id); 

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }
            if (! empty(request()->unsupplier_ids)) {
              $purchases->whereNotIn('contacts.id', request()->unsupplier_ids);
            } else {
              if (! empty(request()->supplier_id)) {
                  $purchases->where('contacts.id', request()->supplier_id);
              }
            }
            if (! empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            if (! empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (! auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }
            
            $purchases = $purchases->with("currency")->orderBy('transaction_date', 'desc')->get();
            $output = Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">'.
                                __('messages.actions').
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if (auth()->user()->can('purchase.view')) {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>'.__('messages.view').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.view')) {
                        $html .= '<li><a href="#" class="print-invoice" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'printInvoice'], [$row->id]).'"><i class="fas fa-print" aria-hidden="true"></i>'.__('messages.print').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.update')) {
                        $html .= '<li><a class="'.($row->canEdit() ? '':'disabled').'" href="'.action([\App\Http\Controllers\PurchaseController::class, 'edit'], [$row->id]).'"><i class="fas fa-edit"></i>'.__('messages.edit').'</a></li>';
                    }
                    if (auth()->user()->can('purchase.delete')) {
                        $html .= '<li><a class="delete-purchase '.($row->canEdit() ? '':'disabled').'" href="'.action([\App\Http\Controllers\PurchaseController::class, 'destroy'], [$row->id]).'"><i class="fas fa-trash"></i>'.__('messages.delete').'</a></li>';
                    }

                    $html .= '<li><a href="'.action([\App\Http\Controllers\LabelsController::class, 'show']).'?purchase_id='.$row->id.'" data-toggle="tooltip" title="'.__('lang_v1.label_help').'"><i class="fas fa-barcode"></i>'.__('barcode.labels').'</a></li>';

                    if (auth()->user()->can('purchase.view')) {
                        if(!empty($row->document)) {
                            $document_name = ! empty(explode('_', $row->document, 2)[1]) ? explode('_', $row->document, 2)[1] : $row->document;
                            $html .= '<li><a href="'.url('uploads/documents/'.$row->document).'" download="'.$document_name.'"><i class="fas fa-download" aria-hidden="true"></i>'.__('purchase.download_document').'</a></li>';
                            if (isFileImage($document_name)) {
                                $html .= '<li><a href="#" data-href="'.url('uploads/documents/'.$row->document).'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>'.__('lang_v1.view_document').'</a></li>';
                            }
                        }
                        if(!empty($row->document2)) {
                            $document_name2 = ! empty(explode('_', $row->document2, 2)[1]) ? explode('_', $row->document2, 2)[1] : $row->document2;
                            $html .= '<li><a href="'.url('uploads/documents/'.$row->document2).'" download="'.$document_name2.'"><i class="fas fa-download" aria-hidden="true"></i>'.__('purchase.download_document').'2</a></li>';
                            if (isFileImage($document_name2)) {
                                $html .= '<li><a href="#" data-href="'.url('uploads/documents/'.$row->document2).'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>'.__('lang_v1.view_document').'2</a></li>';
                            }
                        }
                    }

                    if (auth()->user()->can('purchase.payments') ||
                        auth()->user()->can('edit_purchase_payment') ||
                        auth()->user()->can('delete_purchase_payment')) {
                        $html .= '<li class="divider"></li>';
                        if ($row->payment_status != 'paid') {
                            $html .= '<li><a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$row->id]).'" class="add_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>'.__('purchase.add_payment').'</a></li>';
                        }

                        $html .= '<li><a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->id]).
                        '" class="view_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true" ></i>'.__('purchase.view_payments').'</a></li>';
                    }

                    if (auth()->user()->can('purchase.update')) {
                        $html .= '<li><a href="'.action([\App\Http\Controllers\PurchaseReturnController::class, 'add'], [$row->id]).
                        '"><i class="fas fa-undo" aria-hidden="true" ></i>'.__('lang_v1.purchase_return').'</a></li>';
                    }

                    if (auth()->user()->can('purchase.update') || auth()->user()->can('purchase.update_status')) {
                        $html .= '<li><a href="#" data-purchase_id="'.$row->id.
                        '" data-status="'.$row->status.'" class="update_status"><i class="fas fa-edit" aria-hidden="true" ></i>'.__('lang_v1.update_status').'</a></li>';
                    }

                    if ($row->status == 'ordered') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'new_order']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.new_order_notification').'</a></li>';
                    } elseif ($row->status == 'received') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'items_received']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.item_received_notification').'</a></li>';
                    } elseif ($row->status == 'pending') {
                        $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'items_pending']).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> '.__('lang_v1.item_pending_notification').'</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return ! empty($row->return_exists) ? $row->ref_no.' <small class="label bg-red label-round no-print" title="'.__('lang_v1.some_qty_returned').'"><i class="fas fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn('currency', function($row) use ($business) {
                    return $business->isMainCurrency($row->currency_id) ? $business->currency->symbol : $business->secondCurrency->symbol;
                })
                ->editColumn('final_total', function($row) use ($business) {
                        $total = $row->final_total;
                        $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                        return '<span class="final_total" ' . $data_orig . '="' . $total . '"> '
                                . \App\Utils\Util::format_currency($total, $row->currency, false)
                                . '</span>';
                    }
                )
                ->editColumn('tax_amount', function($row) use ($business) {
                    $tax_amount = $row->tax_amount;
                    $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                    return '<span class="tax_amount" ' . $data_orig . '="' . $tax_amount . '"> '
                        . \App\Utils\Util::format_currency($tax_amount, $row->currency, false)
                        . '</span>';
                })
                ->editColumn('exchange_rate', function($row) use($business) {
                    return $business->isNationCurrency($row->currency_id) ? '1' : $row->nation_exchg_rate;
                })
                ->editColumn('transaction_date', '{{$transaction_date}}')
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn(
                    'status',
                    '<a href="#" @if(auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);

                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )
                ->addColumn('payment_due', function ($row) use($business) {
                    $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>'.__('lang_v1.purchase')
                                . ':</strong> <span class="payment_due" ' . $data_orig . '="'. $due . '">' 
                                . \App\Utils\Util::format_currency($due, $row->currency, false) 
                                . '</span>';

                    if (! empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>'.__('lang_v1.purchase_return').':</strong> <a href="'.action([\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->return_transaction_id]).'" class="view_purchase_return_payment_modal"><span class="purchase_return" data-orig-value="'.$return_due.'">'.$this->transactionUtil->num_f($return_due, true).'</span></a>';
                    }

                    return $due_html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchase.view')) {
                            return  action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'tax_amount', 'exchange_rate', 'action', 'payment_due', 'currency',
                    'payment_status', 'status', 'ref_no', 'name'])
                ->make(true);
            return $output;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();

        return view('purchase.index')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses', 'currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $purchase_currency = $currencies[0];

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        return view('purchase.create')
            ->with(compact('business', 'taxes', 'orderStatuses', 'business_locations', 'currencies', 'purchase_currency', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'common_settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $business = Business::find($business_id);
            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //Check if subscribed or not
            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\PurchaseController::class, 'index']));
            }

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'currency_id' => 'required',
                // 'nation_exchg_rate' => 'required',
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);

            $transaction_data = $request->only([
                'ref_no', 'status', 'contact_id', 'transaction_date', 
                'total_before_tax', 'location_id', 'discount_type', 
                'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 
                'shipping_charges', 'final_total', 'additional_notes', 'nation_exchg_rate', 
                'pay_term_number', 'pay_term_type', 'purchase_order_ids', 'currency_id'
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            if($transaction_data['currency_id'] == $business->currency_id) {
                $transaction_data['exchange_rate'] = 1;
            } else {
                $transaction_data['exchange_rate'] = ($transaction_data['currency_id'] == $business->nation_currency_id) ? 
                                                    1 / $transaction_data['nation_exchg_rate'] : 
                                                    $transaction_data['nation_exchg_rate'];
            }

            if($transaction_data['currency_id'] == $currencies[0]->id) $purchase_currency = $currencies[0];
            else $purchase_currency = $currencies[1];

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $purchase_currency);

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $purchase_currency);
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $purchase_currency);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $purchase_currency);
            $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $purchase_currency);
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $purchase_currency);

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            $transaction_data['document2'] = $this->transactionUtil->uploadFile($request, 'document2', 'documents');

            $transaction_data['custom_field_1'] = $request->input('custom_field_1', null);
            $transaction_data['custom_field_2'] = $request->input('custom_field_2', null);
            $transaction_data['custom_field_3'] = $request->input('custom_field_3', null);
            $transaction_data['custom_field_4'] = $request->input('custom_field_4', null);

            $transaction_data['shipping_custom_field_1'] = $request->input('shipping_custom_field_1', null);
            $transaction_data['shipping_custom_field_2'] = $request->input('shipping_custom_field_2', null);
            $transaction_data['shipping_custom_field_3'] = $request->input('shipping_custom_field_3', null);
            $transaction_data['shipping_custom_field_4'] = $request->input('shipping_custom_field_4', null);
            $transaction_data['shipping_custom_field_5'] = $request->input('shipping_custom_field_5', null);

            if ($request->input('additional_expense_value_1') != '') {
                $transaction_data['additional_expense_key_1'] = $request->input('additional_expense_key_1');
                $transaction_data['additional_expense_value_1'] = $this->productUtil->num_uf($request->input('additional_expense_value_1'), $purchase_currency);
            }

            if ($request->input('additional_expense_value_2') != '') {
                $transaction_data['additional_expense_key_2'] = $request->input('additional_expense_key_2');
                $transaction_data['additional_expense_value_2'] = $this->productUtil->num_uf($request->input('additional_expense_value_2'), $purchase_currency);
            }

            if ($request->input('additional_expense_value_3') != '') {
                $transaction_data['additional_expense_key_3'] = $request->input('additional_expense_key_3');
                $transaction_data['additional_expense_value_3'] = $this->productUtil->num_uf($request->input('additional_expense_value_3'), $purchase_currency);
            }

            if ($request->input('additional_expense_value_4') != '') {
                $transaction_data['additional_expense_key_4'] = $request->input('additional_expense_key_4');
                $transaction_data['additional_expense_value_4'] = $this->productUtil->num_uf($request->input('additional_expense_value_4'), $purchase_currency);
            }

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $purchase_lines = [];
            $purchases = $request->input('purchases');

            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $purchase_currency, $enable_product_editing);

            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            if (! empty($transaction->purchase_order_ids)) {
                $this->transactionUtil->updatePurchaseOrderStatus($transaction->purchase_order_ids);
            }

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $this->transactionUtil->activityLog($transaction, 'added');

            PurchaseCreatedOrModified::dispatch($transaction);
            // Creating notification
            $transaction_data['transaction_id'] = $transaction->id;

            $current_business = Business::find($business_id);
            $data = json_decode($current_business->notification_receivers, true);
            if ($data) {
                $receivers = isset($data['invoice']) ? $data['invoice'] : [];
            } else {
                $data = [];
                $receivers = [];
            }
            if (!in_array(auth()->user()->id, $receivers)) {
                // $receivers[] = auth()->user()->id;
            }
            $transaction_data['receivers'] = $receivers;

            //Auto send notification
            $whatsapp_link = $this->notificationUtil->autoSendNotification($business_id, 'new_order', $transaction, $transaction->contact);
            
            $user = User::find(auth()->user()->id);
            $user->notify(new PurchaseNotification($transaction_data));

            DB::commit();
            $output = ['success' => 1,
                'msg' => __('purchase.purchase_add_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
        // return redirect('purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if (!auth()->user()->can('purchase.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'currency',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.product.second_unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                )
                                ->firstOrFail();
        foreach ($purchase->purchase_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        $payment_methods = $this->productUtil->payment_types($purchase->location_id, true);

        $purchase_taxes = [];
        if (! empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        //Purchase orders
        $purchase_order_nos = '';
        $purchase_order_dates = '';
        if (! empty($purchase->purchase_order_ids)) {
            $purchase_orders = Transaction::find($purchase->purchase_order_ids);

            $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
            $order_dates = [];
            foreach ($purchase_orders as $purchase_order) {
                $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
            }
            $purchase_order_dates = implode(', ', $order_dates);
        }

        $activities = Activity::forSubject($purchase)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        $statuses = $this->productUtil->orderStatuses();

        return view('purchase.show')
                ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes', 'activities', 'statuses', 'purchase_order_nos', 'purchase_order_dates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\PurchaseController::class, 'index']));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]), ]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                'msg' => __('lang_v1.return_exist'), ]);
        }

        $business = Business::find($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();
        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        'purchase_lines.product.second_unit',
                        //'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit',
                        'purchase_lines.purchase_order_line'
                    )
                    ->first();

        if(!$purchase->canEdit()) {
            abort(403, 'This purchase order can not be edit.');
        }

        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $purchase_currency = $purchase->currency;
            
        foreach ($purchase->purchase_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        $purchase_orders = null;
        if (! empty($common_settings['enable_purchase_order'])) {
            $purchase_orders = Transaction::where('business_id', $business_id)
                                        ->where('type', 'purchase_order')
                                        ->where('contact_id', $purchase->contact_id)
                                        ->where(function ($q) use ($purchase) {
                                            $q->where('status', '!=', 'completed');

                                            if (! empty($purchase->purchase_order_ids)) {
                                                $q->orWhereIn('id', $purchase->purchase_order_ids);
                                            }
                                        })
                                        ->pluck('ref_no', 'id');
        }

        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'orderStatuses',
                'business_locations',
                'business',
                'currencies',
                'purchase_currency',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'purchase_orders',
                'common_settings'
            ));
    }

    public function getPurchasesOnDue($contact_id)
    {
        if (! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create') && ! auth()->user()->can('view_own_purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        if (request()->ajax()) {
            $purchases = $this->transactionUtil->getListPurchases($business_id);

            // location condition
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            // supplier_id
            if (! empty($contact_id)) {
                $purchases->where('contacts.id', $contact_id);
            }

            // location_id
            if (! empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $purchases->where('transactions.created_by', $created_by);
                }
            }

            // payment_status (due, partial)
            $payment_status_arr = ['due', 'partial'];
            $purchases->whereIn('transactions.payment_status', $payment_status_arr);

            // transaction date
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $purchases->groupBy('transactions.id');

            $datatable = Datatables::of($purchases)
                ->addColumn(
                    'action',
                    function ($row) use ($is_admin) {
                        $html = '<div class="btn-group d-flex justify-content-center" >
                                    <button type="button" class="btn btn-info btn-xs add-multi-pay" data-purchase-id="'
                                    . $row->id . '">'
                                    . __('messages.add') .
                                '</button></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->addColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('currency', function($row) use($business) {
                    return $business->isMainCurrency($row->currency_id) ? $business->currency->symbol : $business->secondCurrency->symbol;
                })
                ->editColumn(
                    'final_total', function($row) use ($business) {
                        $total = $row->final_total;
                        $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                        return  '<span class="final-total" ' . $data_orig . '="' . $total . '">'
                                . \App\Utils\Util::format_currency($total, $row->currency, false)
                                . '</span>';
                    }
                )
                ->editColumn(
                    'tax_amount', function($row) use($business) {
                        $tax_amount = $row->tax_amount;
                        $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                        return  '<span class="tax-amount" ' . $data_orig . '="' . $tax_amount . '" > ' 
                                . \App\Utils\Util::format_currency($tax_amount, $row->currency, false)
                                . '</span>';
                    }
                )
                ->editColumn(
                    'exchange_rate', function($row) use($business) {
                        return $business->isNationCurrency($row->currency_id) ? '1' : $row->nation_exchg_rate;
                    }
                )
                ->editColumn(
                    'total_paid', function($row) use ($business) {
                        $total_paid = $row->amount_paid;
                        $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                        return  '<span class="total-paid" ' . $data_orig . '="' . $total_paid . '" > ' 
                                . \App\Utils\Util::format_currency($total_paid, $row->currency, false)
                                . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('payment_status', function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )
                ->addColumn('payment_due', function ($row) use($business) {
                    $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                    $total_remaining = $row->final_total - $row->amount_paid;
                    return  '<span class="payment_due" ' . $data_orig . '="' . $total_remaining . '" > '
                            . \App\Utils\Util::format_currency($total_remaining, $row->currency, false)
                            . '</span>';
                })
                ->addColumn('return_due', function ($row) use($business) {
                    if (! empty($row->return_exists)) {
                        $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                        $return_due = $row->amount_return - $row->return_paid;
                        return '<a href="'
                                . action( [\App\Http\Controllers\TransactionPaymentController::class, 'show'], [$row->return_transaction_id]) . '" '
                                . 'class="view_purchase_return_payment_modal">'
                                . '<span class="sell_return_due" '. $data_orig . '="' . $return_due . '"> '
                                . \App\Utils\Util::format_currency($return_due, $row->currency, false)
                                . '</span></a>';
                    } else {
                        return '';
                    }
                });

            $rawColumns = [ 'final_total','action', 'total_paid', 'document_type', 'contact_name',
                            'payment_due','total_remaining', 'payment_status', 'tax_amount', 'return_due', 'status'];

            return $datatable->rawColumns($rawColumns)->make(true);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            if(!$transaction->canEdit()) {
                abort(403, 'This purchase order can not be edit.');
            }

            //Validate document size
            $request->validate([
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
                'document2' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $transaction_before = $transaction->replicate();

            $update_data = $request->only(['ref_no', 'status', 'contact_id',
                'transaction_date', 'total_before_tax',
                'discount_type', 'discount_amount', 'tax_id',
                'tax_amount', 'shipping_details',
                'shipping_charges', 'final_total',
                'currency_id', 'exchange_rate', 'additional_notes', 
                'pay_term_number', 'pay_term_type', 'purchase_order_ids', ]);

            $purchase_currency = $transaction->currency;
    
            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], true);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $purchase_currency);

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $purchase_currency);
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $purchase_currency);
            } else {
                $update_data['discount_amount'] = 0;
            }

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $purchase_currency);
            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $purchase_currency);
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $purchase_currency);
            //unformat input values ends

            $update_data['custom_field_1'] = $request->input('custom_field_1', null);
            $update_data['custom_field_2'] = $request->input('custom_field_2', null);
            $update_data['custom_field_3'] = $request->input('custom_field_3', null);
            $update_data['custom_field_4'] = $request->input('custom_field_4', null);

            $update_data['shipping_custom_field_1'] = $request->input('shipping_custom_field_1', null);
            $update_data['shipping_custom_field_2'] = $request->input('shipping_custom_field_2', null);
            $update_data['shipping_custom_field_3'] = $request->input('shipping_custom_field_3', null);
            $update_data['shipping_custom_field_4'] = $request->input('shipping_custom_field_4', null);
            $update_data['shipping_custom_field_5'] = $request->input('shipping_custom_field_5', null);

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (! empty($document_name)) {
                $update_data['document'] = $document_name;
            }
            $document_name2 = $this->transactionUtil->uploadFile($request, 'document2', 'documents');
            if (! empty($document_name2)) {
                $update_data['document2'] = $document_name2;
            }

            $purchase_order_ids = $transaction->purchase_order_ids ?? [];

            $update_data['additional_expense_key_1'] = $request->input('additional_expense_key_1');
            $update_data['additional_expense_key_2'] = $request->input('additional_expense_key_2');
            $update_data['additional_expense_key_3'] = $request->input('additional_expense_key_3');
            $update_data['additional_expense_key_4'] = $request->input('additional_expense_key_4');

            $update_data['additional_expense_value_1'] = $request->input('additional_expense_value_1') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_1'), $purchase_currency) : 0;
            $update_data['additional_expense_value_2'] = $request->input('additional_expense_value_2') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_2'), $purchase_currency) : 0;
            $update_data['additional_expense_value_3'] = $request->input('additional_expense_value_3') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_3'), $purchase_currency) : 0;
            $update_data['additional_expense_value_4'] = $request->input('additional_expense_value_4') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_4'), $purchase_currency) : 0;

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            //Update transaction payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id);
            $transaction->payment_status = $payment_status;

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $purchase_currency, $enable_product_editing, $before_status);

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $new_purchase_order_ids = $transaction->purchase_order_ids ?? [];
            $purchase_order_ids = array_merge($purchase_order_ids, $new_purchase_order_ids);
            if (! empty($purchase_order_ids)) {
                $this->transactionUtil->updatePurchaseOrderStatus($purchase_order_ids);
            }

            //Auto send notification
            $whatsapp_link = $this->notificationUtil->autoSendNotification($business_id, 'new_order', $transaction, $transaction->contact);

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            PurchaseCreatedOrModified::dispatch($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_update_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return response()->json($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist'),
                    ];

                    return $output;
                }

                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();

                //Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale'),
                    ];

                    return $output;
                }

                $delete_purchase_lines = $transaction->purchase_lines;
                DB::beginTransaction();

                $log_properities = [
                    'id' => $transaction->id,
                    'ref_no' => $transaction->ref_no,
                ];
                $this->transactionUtil->activityLog($transaction, 'purchase_deleted', $log_properities);

                $transaction_status = $transaction->status;
                if ($transaction_status != 'received') {
                    $transaction->delete();
                } else {
                    //Delete purchase lines first
                    $delete_purchase_line_ids = [];
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity
                        );
                    }
                    PurchaseLine::where('transaction_id', $transaction->id)
                                ->whereIn('id', $delete_purchase_line_ids)
                                ->delete();

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
                }

                //Delete Transaction
                $transaction->delete();

                //Delete account transactions
                AccountTransaction::where('transaction_id', $id)->delete();

                PurchaseCreatedOrModified::dispatch($transaction, true);

                DB::commit();

                $output = ['success' => true,
                    'msg' => __('lang_v1.purchase_delete_success'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id)
                            ->active();

            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                                ->orWhere('supplier_business_name', 'like', '%'.$term.'%')
                                ->orWhere('contacts.contact_id', 'like', '%'.$term.'%');
            })
                        ->select(
                            'contacts.id',
                            DB::raw('IF(name="", supplier_business_name, name) as text'),
                            'supplier_business_name as business_name',
                            'contacts.mobile',
                            'contacts.address_line_1',
                            'contacts.address_line_2',
                            'contacts.city',
                            'contacts.state',
                            'contacts.country',
                            'contacts.zip_code',
                            'contacts.contact_id',
                            'contacts.pay_term_type',
                            'contacts.pay_term_number',
                            'contacts.balance'
                        )
                        ->onlySuppliers()
                        ->get();

            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->term;
            $search_fields = request()->get('search_fields', ['name', 'sku']);
            if (in_array('sku', $search_fields)) {
                $search_fields[] = 'sub_sku';
            }

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            $only_variations = false;
            if (isset(request()->only_variations)) {
                $only_variations = filter_var(request()->only_variations, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $q = Product::leftJoin('variations','products.id','=','variations.product_id')
                ->where(function ($query) use ($term, $search_fields) {
                    $query->where('products.name', 'like', '%'.$term.'%');
                    foreach($search_fields as $key => $field) {
                        if($field == 'name') continue;
                        $query->orWhere($field, 'like', '%'.$term.'%');
                    }
                })
                ->active()
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            if (! empty(request()->location_id)) {
                $q->ForLocation(request()->location_id);
            }
            $products = $q->get();

            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                = [
                    'variation_id' => $product->variation_id,
                    'variation_name' => $product->variation,
                    'sub_sku' => $product->sub_sku,
                ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (! empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single' && ! $only_variations) {
                        $result[] = ['id' => $i,
                            'text' => $value['name'].' - '.$value['sku'],
                            'variation_id' => 0,
                            'product_id' => $key,
                        ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text.' ('.$variation['variation_name'].')';
                        }
                        $i++;
                        $result[] = ['id' => $i,
                            'text' => $text.' - '.$variation['sub_sku'],
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                        ];
                    }
                    $i++;
                }
            }

            return json_encode($result);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseEntryRow(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');
            $supplier_id = $request->input('supplier_id');
            $p_currency_id = $request->input('purchase_currency_id');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $purchase_currency = Currency::find($p_currency_id);;

            if (! empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit', 'second_unit'])
                                    ->first();

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);

                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation',
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    },
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations = $query->get();
                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

                $last_purchase_line = $this->getLastPurchaseLine($variation_id, $location_id, $supplier_id);

                return view('purchase.partials.purchase_entry_row')
                    ->with(compact(
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'purchase_currency',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order',
                        'last_purchase_line'
                    ));
            }
        }
    }

    /**
     * Finds last purchase line of a variation for the supplier for a location
     */
    private function getLastPurchaseLine($variation_id, $location_id, $supplier_id = null)
    {
        $query = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id',
                        '=', 't.id')
                        ->where('t.location_id', $location_id)
                        ->where('t.type', 'purchase')
                        ->where('t.status', 'received')
                        ->where('purchase_lines.variation_id', $variation_id);

        if (! empty($supplier_id)) {
            $query = $query->where('t.contact_id', '=', $supplier_id);
        }
        $purchase_line = $query->orderBy('transaction_date', 'desc')
                            ->select('purchase_lines.*')
                            ->first();

        return $purchase_line;
    }

    public function importPurchaseProducts(Request $request)
    {
        try {
            $file = $request->file('file');

            $parsed_array = Excel::toArray([], $file);
            //Remove header row
            $imported_data = array_splice($parsed_array[0], 1);

            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $row_count = $request->input('row_count');

            $formatted_data = [];
            $row_index = 0;
            $error_msg = '';
            foreach ($imported_data as $key => $value) {
                $row_index = $key + 1;
                $temp_array = [];

                if (! empty($value[0])) {
                    $variation = Variation::join('products as p', 'p.id', 'variations.product_id')
                                        ->where('sub_sku', trim($value[0]))
                                        ->where('p.business_id', $business_id)
                                        ->with([
                                            'product_variation',
                                            'variation_location_details' => function ($q) use ($location_id) {
                                                $q->where('location_id', $location_id);
                                            },
                                        ])
                                        ->select('variations.*')
                                        ->first();

                    $temp_array['variation'] = $variation;

                    if (empty($variation)) {
                        $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                        break;
                    }

                    $product = Product::where('sku', trim($value[0]))
                                    ->where('business_id', $business_id)
                                    ->with(['unit'])
                                    ->first();

                    if (empty($product)) {
                        $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                        break;
                    }

                    $temp_array['product'] = $product;

                    $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product->id);

                    $temp_array['sub_units'] = $sub_units;
                } else {
                    $error_msg = __('lang_v1.product_not_found_exception', ['row' => $row_index, 'sku' => $value[0]]);
                    break;
                }

                if (! empty($value[0])) {
                    $temp_array['quantity'] = $value[1];
                } else {
                    $error_msg = __('lang_v1.quantity_required', ['row' => $row_index]);
                    break;
                }

                $temp_array['unit_cost_before_discount'] = ! empty($value[2]) ? $value[2] : $variation->default_purchase_price;
                $temp_array['discount_percent'] = ! empty($value[3]) ? $value[3] : 0;

                $tax_id = null;

                if (! empty($value[4])) {
                    $tax_name = trim($value[4]);
                    $tax = TaxRate::where('business_id', $business_id)
                                ->where('name', 'like', "%{$tax_name}%")
                                ->first();

                    $tax_id = $tax->id ?? $tax_id;
                }

                $temp_array['tax_id'] = $tax_id;
                $temp_array['lot_number'] = ! empty($value[5]) ? $value[5] : null;
                $temp_array['mfg_date'] = ! empty($value[6]) ? $this->productUtil->format_date($value[6]) : null;
                $temp_array['exp_date'] = ! empty($value[7]) ? $this->productUtil->format_date($value[7]) : null;

                $formatted_data[] = $temp_array;
            }

            if (! empty($error_msg)) {
                return [
                    'success' => false,
                    'msg' => $error_msg,
                ];
            }

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $purchase_currency = $currencies[0];

            $html = view('purchase.partials.imported_purchase_product_rows')
                        ->with(compact('formatted_data', 'taxes', 'purchase_currency', 'hide_tax', 'row_count'))->render();

            return [
                'success' => true,
                'msg' => __('lang_v.imported'),
                'html' => $html,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
    }

    public function getPurchaseOrderLines($purchase_order_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $purchase_order = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_order')
                        ->with(['purchase_lines', 'purchase_lines.variations',
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation', ])
                        ->findOrFail($purchase_order_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

        $sub_units_array = [];
        foreach ($purchase_order->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';

        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $purchase_currency = $currencies[0];

        $row_count = request()->input('row_count');

        $html = view('purchase.partials.purchase_order_lines')
                ->with(compact(
                    'purchase_order',
                    'taxes',
                    'hide_tax',
                    'purchase_currency',
                    'row_count',
                    'sub_units_array'
                ))->render();

        return [
            'html' => $html,
            'po' => $purchase_order,
        ];
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (! empty($contact_id) && ! empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                            ->where('ref_no', $ref_no)
                            ->where('contact_id', $contact_id);
            if (! empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'currency',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'payment_lines'
                                    )
                                    ->first();
            $payment_methods = $this->productUtil->payment_types(null, false, $business_id);

            //Purchase orders
            $purchase_order_nos = '';
            $purchase_order_dates = '';
            if (! empty($purchase->purchase_order_ids)) {
                $purchase_orders = Transaction::find($purchase->purchase_order_ids);

                $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
                $order_dates = [];
                foreach ($purchase_orders as $purchase_order) {
                    $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
                }
                $purchase_order_dates = implode(', ', $order_dates);
            }

            $output = ['success' => 1, 'receipt' => [], 'print_title' => $purchase->ref_no];
            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods', 'purchase_order_nos', 'purchase_order_dates'))->render();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Update purchase status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (! auth()->user()->can('purchase.update') && ! auth()->user()->can('purchase.update_status')) {
            abort(403, 'Unauthorized action.');
        }
        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {
            return ['success' => 0,
                'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]), ];
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                ->where('type', 'purchase')
                                ->with(['purchase_lines'])
                                ->findOrFail($request->input('purchase_id'));

            $before_status = $transaction->status;

            $update_data['status'] = $request->input('status');

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $purchase_currency = $currencies[0];
            
            foreach ($transaction->purchase_lines as $purchase_line) {
                $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $purchase_currency);
            }

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('purchase.purchase_update_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }
}
