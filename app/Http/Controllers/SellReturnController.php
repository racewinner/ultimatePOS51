<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Business;
use App\Events\TransactionPaymentDeleted;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\InvoiceLayout;
use App\CashRegisterTransaction;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $contactUtil;

    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ContactUtil $contactUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        if (request()->ajax()) {
            // To include invoice_no field in columns of request so that invoice_no field would be able to be sought.
            $req_columns = request()->input('columns');
            $req_columns[] = [
                'data' => 'transactions.pymo_serie',
            ];
            $req_columns[] = [
                'data' => 'c.supplier_business_name',
            ];
            $req_columns[] = [
                'data' => 'c.name',
            ];
            request()->merge(['columns' => $req_columns]);

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join('business_locations AS bl','transactions.location_id','=','bl.id')
                    ->join('transactions as T1','transactions.return_parent_id','=','T1.id')
                    ->leftJoin('transaction_payments AS tp','transactions.id','=','tp.transaction_id')
                    ->leftJoin('contacts as c','c.id', '=', 'transactions.contact_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return')
                    ->where('transactions.status', 'final')
                    ->with(['currency', 'authorizor'])
                    ->select(
                        'transactions.id',
                        'transactions.business_id',
                        'transactions.currency_id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'transactions.pymo_invoice',
                        'transactions.pymo_serie',
                        'transactions.document_type',
                        'transactions.authorized_by',
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        'T1.pymo_invoice as parent_pymo_invoice',
                        'T1.pymo_serie as parent_pymo_serie',
                        'T1.document_type as parent_document_type',
                        'T1.invoice_token as parent_invoice_token',
                        DB::raw('SUM(tp.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (! empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            // Add condition for authorized_by
            if (request()->has('authorized_by')) {
                $authorized_by = request()->get('authorized_by');
                if(!empty($authorized_by)) {
                    $sells->where('transactions.authorized_by', $authorized_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (! empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">'.
                            __('messages.actions').
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'App\Http\Controllers\SellReturnController@show\', [$parent_sale_id])}}"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
                        <li><a class="@if(!empty($pymo_invoice)) disabled @endif" href="{{action(\'App\Http\Controllers\SellReturnController@add\', [$parent_sale_id])}}" ><i class="fa fa-edit" aria-hidden="true"></i> @lang("messages.edit")</a></li>
                        <li><a class="delete_sell_return @if(!empty($pymo_invoice)) disabled @endif" href="{{action(\'App\Http\Controllers\SellReturnController@destroy\', [$id])}}" ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</a></li>
                        <li><a href="#" class="return-invoice-print" data-id="{{$id}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>

                    @if($payment_status != "paid")
                        <li><a href="{{action(\'App\Http\Controllers\TransactionPaymentController@addPayment\', [$id])}}" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a></li>
                    @endif
                        
                    <li><a href="{{action(\'App\Http\Controllers\TransactionPaymentController@show\', [$id])}}" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.view_payments")</a></li>
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn('invoice_no', function($row) {
                    return $row->pymo_serie ?? $row->invoice_no;
                })
                ->editColumn('document_type', function($row) {
                    $html = '';
                    if($row->pymo_invoice) {
                        $html = "<a href='/uploads/invoices/" . $row->business_id . '/' . $row->pymo_invoice . ".pdf' class='ms-2' target='_blank'>" . $row->document_type . "</a>";
                    } else {
                        $html = "<a href='/invoice/transaction/" . $row->id . "' class='ms-2' target='_blank'>". __('invoice.internal') ."</a>";
                    }
                    return $html;
                })
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->parent_sale_id]).'">'.$row->parent_sale.'</button>';
                })
                ->addColumn('parent_invoice', function($row) {
                    $html = '';
                    if($row->parent_pymo_invoice) {
                        $html = "<a href='/uploads/invoices/" . $row->business_id . '/' . $row->parent_pymo_invoice . ".pdf' class='ms-2' target='_blank'>" 
                                ."<div>". $row->parent_document_type . "</div>"
                                ."<div>( ". $row->parent_pymo_serie ." )</div></a>";
                    } else {
                        if($row->parent_invoice_token) {
                            $html = "<a href='/invoice/" . $row->parent_invoice_token . "' class='ms-2' target='_blank'>". __('invoice.internal') ."</a>";
                        }
                    }
                    return $html;
                })
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'show\'], [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->editColumn('final_total', function($row) use ($business) {
                    $final_total = $row->final_total;
                    $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                    return '<span class="final_total" '.$data_orig.'="'.$final_total.'">'. Util::format_currency($final_total, $row->currency).'</span>';
                })
                ->addColumn('payment_due', function ($row) use ($business) {
                    $due = $row->final_total - $row->amount_paid;
                    $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                    return '<span class="payment_due" '.$data_orig.'="'.$due.'">'. Util::format_currency($due, $row->currency) . '</sapn>';
                })
                ->addColumn('authorizor', function($row) {
                    if( !empty($row->authorizor) ) {
                        return $row->authorizor->getDisplayName();
                    } else {
                        return "";
                    }
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view')) {
                            return  action([\App\Http\Controllers\SellReturnController::class, 'show'], [$row->parent_sale_id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'action', 'invoice_no', 'document_type', 'parent_sale', 
                              'parent_invoice', 'payment_status', 'payment_due', 'name'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        // To get authorizors
        $authorizors = User::forDropdown($business_id, false, false, true, false, true);

        $sales_representative = User::forDropdown($business_id, false, false, true);
        $pdf_formats = [
            'normal' => 'Normal(A4)',
            'ticket' => 'Ticket'
        ];

        return view('sell_return.index')->with(compact('business_locations', 'authorizors', 'customers', 'sales_representative', 'currencies', 'pdf_formats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     if (!auth()->user()->can('sell.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');

    //     //Check if subscribed or not
    //     if (!$this->moduleUtil->isSubscribed($business_id)) {
    //         return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\SellReturnController::class, 'index']));
    //     }

    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

    //     return view('sell_return.create')
    //         ->with(compact('business_locations'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = auth()->user()->hasRole('Admin#'.session('business.id')) ? true : false;

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines','currency', 'location', 'return_parent', 'contact', 'tax', 'sell_lines.sub_unit', 'sell_lines.product', 'sell_lines.product.unit'])
                            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }

            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity, false, null, true);
        }

        //Check if any sell return exists for the sale
        $sell_return = Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return')
                ->where('return_parent_id', $sell->id)
                ->first();

        if( !empty($sell_return) && !$sell_return->canEdit()) {
            abort(403, 'This transaction can not be edit.');
        }

        $pos_settings = json_decode($business->pos_settings);
        $enable_pin = !empty($pos_settings->enable_pin) && !empty(auth()->user()->enable_pin);
        $pdf_formats = [
            'normal' => 'Normal(A4)',
            'ticket' => 'Ticket'
        ];

        return view('sell_return.add')
            ->with(compact(['sell', 'sell_return', 'enable_pin', 'pdf_formats']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $is_admin = auth()->user()->hasRole('Admin#'.session('business.id')) ? true : false;
            $input = $request->except('_token');

            if (! empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
                $business = Business::where('id', $business_id)->first();

                // To check pin
                $pos_settings = json_decode($business->pos_settings);
                $enable_pin = !empty($pos_settings->enable_pin) && !empty(auth()->user()->enable_pin);
                if($enable_pin) {
                    $pin_user = User::where('pin', $input['pin'])->first();
                    if(empty($pin_user)) {
                        return [
                            'success' => 0,
                            'msg' => 'PIN inválido'
                        ];
                    }

                    $input['authorized_by'] = $pin_user->id;
                }

                //Check if subscribed or not
                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\SellReturnController::class, 'index']));
                }

                $user_id = $request->session()->get('user.id');

                DB::beginTransaction();

                // To update sell_return
                $sell_return = $this->transactionUtil->addSellReturn($input, $business_id, $user_id);

                $receipt = $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id, $input['pdf_format'], null, $pin_user);

                DB::commit();

                $output = ['success' => 1,
                    'msg' => __('lang_v1.success'),
                    'receipt' => $receipt,
                    'sell_return_id' => $sell_return->id
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = ['success' => 0,
                'msg' => $msg,
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $query = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'return_parent',
                                    'tax',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'sell_lines.sub_unit',
                                    'sell_lines.product',
                                    'sell_lines.product.unit',
                                    'location'
                                );

        if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
            $query = $query->where('created_by', request()->session()->get('user.id'));
        }
        $sell = $query->first();

        foreach ($sell->sell_lines as $key => $value) {
            if (! empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $sell_taxes = [];
        if (! empty($sell->return_parent->tax)) {
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }

        $total_discount = 0;
        if ($sell->return_parent->discount_type == 'fixed') {
            $total_discount = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'percentage') {
            $discount_percent = $sell->return_parent->discount_amount;
            if ($discount_percent == 100) {
                $total_discount = $sell->return_parent->total_before_tax;
            } else {
                $total_after_discount = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
                $total_before_discount = $total_after_discount * 100 / (100 - $discount_percent);
                $total_discount = $total_before_discount - $total_after_discount;
            }
        }

        $activities = Activity::forSubject($sell->return_parent)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount', 'activities'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('access_sell_return') && ! auth()->user()->can('access_own_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                //Begin transaction
                DB::beginTransaction();

                $query = Transaction::where('id', $id)
                    ->where('business_id', $business_id)
                    ->where('type', 'sell_return')
                    ->with(['sell_lines', 'payment_lines']);

                if (! auth()->user()->can('access_sell_return') && auth()->user()->can('access_own_sell_return')) {
                    $sells->where('created_by', request()->session()->get('user.id'));
                }
                $sell_return = $query->first();

                $sell_lines = TransactionSellLine::where('transaction_id',
                                            $sell_return->return_parent_id)
                                    ->get();

                if (! empty($sell_return)) {
                    $transaction_payments = $sell_return->payment_lines;

                    foreach ($sell_lines as $sell_line) {
                        if ($sell_line->quantity_returned > 0) {
                            $quantity = 0;
                            $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);

                            $sell_line->quantity_returned = 0;
                            $sell_line->save();

                            //update quantity sold in corresponding purchase lines
                            $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, 0, $quantity_before);

                            // Update quantity in variation location details
                            $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, 0, $quantity_before);
                        }
                    }

                    $sell_return->delete();
                    foreach ($transaction_payments as $payment) {
                        event(new TransactionPaymentDeleted($payment));
                    }

                    // To delete cash_register_transaction if any
                    CashRegisterTransaction::where("transaction_id", $sell_return->id)->where("transaction_type", "refund")->delete();
                }

                // To update payment_status
                $this->transactionUtil->updatePaymentStatus($sell_return->return_parent_id);

                DB::commit();
                $output = ['success' => 1,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();

                if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                    $msg = $e->getMessage();
                } else {
                    \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                    $msg = __('messages.something_went_wrong');
                }

                $output = ['success' => 0,
                    'msg' => $msg,
                ];
            }

            return $output;
        }
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param  string  $printer_type = null
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $pdf_format = null,
        $printer_type = null,
        $pin_user = null
    ) {
        $output = ['is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        // To get causer
        $activity_log = Activity::where('subject_id', $transaction_id)->first(); 
        if(!empty($activity_log)) $causer = User::find($activity_log->causer_id);

        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

            //If print type browser - return the content, printer - return printer config data, and invoice format config
            $output['print_title'] = $receipt_details->invoice_no;
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                if($pdf_format == 'normal') {
                    $output['html_content'] = view('sell_return.receipt', compact('receipt_details', 'causer', 'pin_user'))->render();
                } else if($pdf_format == 'ticket') {
                    $output['html_content'] = view('sell_return.receipt_ticket', compact('receipt_details', 'causer', 'pin_user'))->render();
                }
            }
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id, $pdf_format)
    {
        if (request()->ajax()) {
            try {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];

                $business_id = $request->session()->get('user.business_id');

                $transaction = Transaction::where('business_id', $business_id)
                                ->where('id', $transaction_id)
                                ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, $pdf_format, 'browser', $transaction->authorizor);

                if (! empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Function to validate sell for sell return
     */
    public function validateInvoiceToReturn($invoice_no)
    {
        if (! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access') && ! auth()->user()->can('view_own_sell_only')) {
            return ['success' => 0,
                'msg' => trans('lang_v1.permission_denied'),
            ];
        }

        $business_id = request()->session()->get('user.business_id');
        $query = Transaction::where('business_id', $business_id)
                            ->where('invoice_no', $invoice_no);

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (! auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('created_by', auth()->user()->id);
        }

        $sell = $query->first();

        if (empty($sell)) {
            return ['success' => 0,
                'msg' => trans('lang_v1.sell_not_found'),
            ];
        }

        return ['success' => 1,
            'redirect_url' => action([\App\Http\Controllers\SellReturnController::class, 'add'], [$sell->id]),
        ];
    }
}
