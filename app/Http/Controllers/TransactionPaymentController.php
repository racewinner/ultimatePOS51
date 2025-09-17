<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Business;
use App\BusinessLocation;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentUpdated;
use App\Exceptions\AdvanceBalanceNotAvailable;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Notifications\PaymentNotification;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Datatables;
use DB;
use Illuminate\Http\Request;

class TransactionPaymentController extends Controller
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payments_representative = User::forDropdown($business_id, false, false, true);

        $business = Business::find($business_id);

        $payments = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
            ->leftjoin('transaction_payments as parent_payment', 'transaction_payments.parent_id', '=', 'parent_payment.id')
            ->leftjoin('contacts as c', 'c.id', '=', 'transaction_payments.payment_for')
            ->where('transaction_payments.business_id', $business_id)
            ->whereNull('transaction_payments.parent_id')
            ->with(['child_payments','contact', 'child_payments.transaction']);

        $trans_type = request()->trans_type;
        if($trans_type == 'purchase') $payments->where('t.type', 'purchase');

        $formattedStartDate = null;
        $formattedEndDate = null;
        
        if(!empty(request()->customer_id)) $payments->where('transaction_payments.payment_for', request()->customer_id);
        if(!empty(request()->location_id)) $payments->where('t.location_id', request()->location_id);
        if(!empty(request()->created_by)) $payments->where('created_by', request()->created_by);
        if(!empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $payments->whereDate('transaction_payments.paid_on', '>=', $start)
                        ->whereDate('transaction_payments.paid_on', '<=', $end);

            // To convert date format so that frontend can understand.
            $datetime = new \DateTime($start);
            $formattedStartDate = $datetime->format('d/m/Y');

            $datetime = new \DateTime($end);
            $formattedEndDate = $datetime->format('d/m/Y');
        }
        if(!empty(request()->searchTerm)) {
            $searchTerm = request()->searchTerm;
            $payments->where(function($query) use($searchTerm) {
                $query->where('transaction_payments.payment_ref_no', 'like', ["%{$searchTerm}%"])
                    ->orWhere('transaction_payments.pymo_serie', 'like', ["%{$searchTerm}%"])
                    ->orWhere('t.pymo_serie', 'like', ["%{$searchTerm}%"])
                    ->orWhere('c.supplier_business_name', 'like', ["%{$searchTerm}%"])
                    ->orWhere('c.name', 'like', ["%{$searchTerm}%"]);
            });
        }

        $payments = $payments->select(
                'transaction_payments.id',
                'transaction_payments.business_id',
                'transaction_payments.amount',
                'transaction_payments.is_return',
                'transaction_payments.method',
                'transaction_payments.paid_on',
                'transaction_payments.payment_ref_no',
                'transaction_payments.parent_id',
                'transaction_payments.payment_for',
                'transaction_payments.transaction_no',
                'transaction_payments.pymo_invoice',
                'transaction_payments.pymo_serie',
                'transaction_payments.document_type',
                'transaction_payments.cheque_number',
                'transaction_payments.card_transaction_number',
                'transaction_payments.bank_account_number',
                'transaction_payments.id as DT_RowId',
                't.invoice_no',
                't.ref_no',
                't.currency_id',
                't.type as transaction_type',
                't.return_parent_id',
                't.id as transaction_id',
                't.pymo_invoice as parent_pymo_invoice',
                't.pymo_serie as parent_pymo_serie',
                't.invoice_token as parent_invoice_token',
                't.document_type as parent_document_type',
                'parent_payment.payment_ref_no as parent_payment_ref_no'
            )
            ->groupBy('transaction_payments.id')
            ->orderByDesc('transaction_payments.paid_on')
            ->paginate(15, request()->page ?? 1);

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view("transaction_payment.index")->with(compact('business_locations', 'customers', 'payments_representative',
            'payments', 'business', 'payment_types', 'formattedStartDate', 'formattedEndDate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $transaction_id = $request->input('transaction_id');
            $transaction = Transaction::where('business_id', $business_id)->with(['contact'])->findOrFail($transaction_id);

            $transaction_before = $transaction->replicate();

            if (! (auth()->user()->can('purchase.payments') || auth()->user()->can('sell.payments') || auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense'))) {
                abort(403, 'Unauthorized action.');
            }

            if ($transaction->payment_status != 'paid') {
                $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                    'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                    'cheque_number', 'bank_account_number', ]);
                $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
                $inputs['transaction_id'] = $transaction->id;
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['payment_for'] = $transaction->contact_id;

                if ($inputs['method'] == 'custom_pay_1') {
                    $inputs['transaction_no'] = $request->input('transaction_no_1');
                } elseif ($inputs['method'] == 'custom_pay_2') {
                    $inputs['transaction_no'] = $request->input('transaction_no_2');
                } elseif ($inputs['method'] == 'custom_pay_3') {
                    $inputs['transaction_no'] = $request->input('transaction_no_3');
                }

                if (! empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                    $inputs['account_id'] = $request->input('account_id');
                }

                $prefix_type = 'purchase_payment';
                if (in_array($transaction->type, ['sell', 'sell_return'])) {
                    $prefix_type = 'sell_payment';
                } elseif (in_array($transaction->type, ['expense', 'expense_refund'])) {
                    $prefix_type = 'expense_payment';
                }

                DB::beginTransaction();

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
                $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
                $inputs['document2'] = $this->transactionUtil->uploadFile($request, 'document2', 'documents');

                //Pay from advance balance
                $payment_amount = $inputs['amount'];
                $contact_balance = ! empty($transaction->contact) ? $transaction->contact->balance : 0;
                if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance) {
                    throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                }

                if (! empty($inputs['amount'])) {
                    $tp = TransactionPayment::create($inputs);

                    if (! empty($request->input('denominations'))) {
                        $this->transactionUtil->addCashDenominations($tp, $request->input('denominations'));
                    }

                    $inputs['transaction_type'] = $transaction->type;
                    event(new TransactionPaymentAdded($tp, $inputs));
                }

                //update payment status
                $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                $transaction->payment_status = $payment_status;

                $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

                // Creating notification
                $transaction_data['transaction_id'] = $transaction->id;

                $current_business = Business::find($business_id);
                $data = json_decode($current_business->notification_receivers, true);
                if ($data) {
                    $receivers = isset($data['payment']) ? $data['payment'] : [];
                } else {
                    $data = [];
                    $receivers = [];
                }
                if (!in_array(auth()->user()->id, $receivers)) {
                    // $receivers[] = auth()->user()->id;
                }
                $transaction_data['receivers'] = $receivers;
                $user = User::find(auth()->user()->id);
                $user->notify(new PaymentNotification($transaction_data));
                
                DB::commit();
            }

            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = __('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            }

            $output = ['success' => false,
                'msg' => $msg,
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $transaction = Transaction::where('id', $id)
                                        ->with(['contact', 'business', 'transaction_for', 'currency'])
                                        ->first();
            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts_enabled = true;
                $payments_query->with(['payment_account']);
            }

            $payments = $payments_query->get();
            $location_id = ! empty($transaction->location_id) ? $transaction->location_id : null;
            $payment_types = $this->transactionUtil->payment_types($location_id, true);

            return view('transaction_payment.show_payments')
                    ->with(compact('transaction', 'payments', 'payment_types', 'accounts_enabled'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $payment_line = TransactionPayment::with(['denominations'])->where('method', '!=', 'advance')->findOrFail($id);
            $transaction = Transaction::where('id', $payment_line->transaction_id)
                                        ->where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->first();
            $trans_type = $transaction->type;

            if($payment_line->multi_pay) {
                $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

                if($trans_type == 'sell') $trans = $this->transactionUtil->getListSells($business_id, 'sell'); 
                else if($transaction->type == 'purchase') $trans = $this->transactionUtil->getListPurchases($business_id); 

                $trans->where('tp.multi_pay', 1)
                    ->where('tp.payment_ref_no', $payment_line->payment_ref_no)
                    ->with(['currency'])
                    ->addSelect(
                        'tp.amount as pay_amount', 
                        'tp.payment_ref_no', 
                        'tp.paid_on',
                        'tp.id as payment_id'
                    );
                $trans->groupBy('transactions.id');
                $multi_pays = $trans->get();

                return view("transaction_payment.edit_multi_payment_modal")->with(compact('payment_line', 'multi_pays', 'payment_types', 'trans_type'));
            } else {
                $payment_types = $this->transactionUtil->payment_types($transaction ? $transaction->location : null);

                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);
    
                return view('transaction_payment.edit_payment_row')
                            ->with(compact('transaction', 'payment_types', 'payment_line', 'accounts', 'currencies'));
            }
        }
    }

    public function updateMultiPayment(Request $request)
    {
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $inputs = $request->only(['method', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number', 'payment_ref_no']);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            
            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            $pays = json_decode($request->input('pays'), true);
            $inputs['mulity_pay'] = count($pays) > 1 ? true : false;

            DB::beginTransaction();

            foreach($pays as $key => $pay) {
                if($pay['amount'] == 0) continue;

                $id = $pay['payment_id'];
                $inputs['amount'] = $this->transactionUtil->num_uf($pay['amount']);

                $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

                // To upload document if any
                $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
                if (! empty($document_name)) {
                    $inputs['document'] = $document_name;
                }

                //Update parent payment if exists
                if (! empty($payment->parent_id)) {
                    $parent_payment = TransactionPayment::find($payment->parent_id);
                    $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);

                    $parent_payment->save();
                }

                // To update payment
                $payment->update($inputs);

                if(!empty($payment->transaction_id)) {
                    $transaction = Transaction::where('business_id', $business_id)->find($payment->transaction_id);
                    $transaction_before = $transaction->replicate();
    
                    //update payment status
                    $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
                    $transaction->payment_status = $payment_status;
    
                    $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
    
                    // Creating notification
                    $transaction_data['transaction_id'] = $transaction->id;
    
                    $current_business = Business::find($business_id);
                    $receivers = explode(',', $current_business->notification_receivers);
                    if (!in_array(auth()->user()->id, $receivers)) {
                        // $receivers[] = auth()->user()->id;
                    }
                    $transaction_data['receivers'] = $receivers;
                    $user = User::find(auth()->user()->id);
                    $user->notify(new PaymentNotification($transaction_data));
                }
            }

            DB::commit();

            $output = ['success' => true,
                'msg' => __('purchase.payment_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return json_encode($output);
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
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number']);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (! empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);
            if (! empty($request->input('denominations'))) {
                $this->transactionUtil->updateCashDenominations($payment, $request->input('denominations'));
            }

            // To upload document if any
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (! empty($document_name)) {
                $inputs['document'] = $document_name;
            }

            // if this payment is not for any transaction, put the currency_id and nation_exchg_rate
            if(!$payment->transaction_id) {
                $inputs['currency_id'] = $request->input('currency_id');
                $inputs['nation_exchg_rate'] = $request->input('nation_exchg_rate');
            }

            DB::beginTransaction();

            //Update parent payment if exists
            if (! empty($payment->parent_id)) {
                $parent_payment = TransactionPayment::find($payment->parent_id);
                $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);

                $parent_payment->save();
            }
            
            // To update payment
            $payment->update($inputs);

            if(!empty($payment->transaction_id)) {
                $transaction = Transaction::where('business_id', $business_id)->find($payment->transaction_id);
                $transaction_before = $transaction->replicate();

                //update payment status
                $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
                $transaction->payment_status = $payment_status;

                $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

                // Creating notification
                $transaction_data['transaction_id'] = $transaction->id;

                $current_business = Business::find($business_id);
                $receivers = explode(',', $current_business->notification_receivers);
                if (!in_array(auth()->user()->id, $receivers)) {
                    // $receivers[] = auth()->user()->id;
                }
                $transaction_data['receivers'] = $receivers;
                $user = User::find(auth()->user()->id);
                $user->notify(new PaymentNotification($transaction_data));
            }

            DB::commit();

            //event
            event(new TransactionPaymentUpdated($payment, $transaction->type));

            $output = ['success' => true,
                'msg' => __('purchase.payment_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('delete_purchase_payment') && ! auth()->user()->can('delete_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $payment = TransactionPayment::findOrFail($id);

                DB::beginTransaction();

                if (! empty($payment->transaction_id)) {
                    TransactionPayment::deletePayment($payment);
                } else { //advance payment
                    $adjusted_payments = TransactionPayment::where('parent_id',
                                                $payment->id)
                                                ->get();

                    $total_adjusted_amount = $adjusted_payments->sum('amount');

                    //Get customer advance share from payment and deduct from advance balance
                    $total_customer_advance = $payment->amount - $total_adjusted_amount;
                    if ($total_customer_advance > 0) {
                        $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance, 'deduct');
                    }

                    //Delete all child payments
                    foreach ($adjusted_payments as $adjusted_payment) {
                        //Make parent payment null as it will get deleted
                        $adjusted_payment->parent_id = null;
                        TransactionPayment::deletePayment($adjusted_payment);
                    }

                    //Delete advance payment
                    TransactionPayment::deletePayment($payment);
                }

                DB::commit();

                $output = ['success' => true,
                    'msg' => __('purchase.payment_deleted_success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($transaction_id)
    {
        if (! auth()->user()->can('purchase.payments') && ! auth()->user()->can('sell.payments') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                        ->with(['contact', 'location', 'currency'])
                                        ->findOrFail($transaction_id);
            if ($transaction->payment_status != 'paid') {
                $show_advance = in_array($transaction->type, ['sell', 'purchase']) ? true : false;
                $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);

                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);
                $amount = $transaction->final_total - $paid_amount;
                if ($amount < 0) {
                    $amount = 0;
                }

                $amount_formated = $this->transactionUtil->num_f($amount);

                $payment_line = new TransactionPayment();
                $payment_line->amount = $amount;
                $payment_line->method = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();

                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

                $view = view('transaction_payment.payment_row')
                ->with(compact('transaction', 'payment_types', 'payment_line', 'amount_formated', 'accounts'))->render();

                $output = ['status' => 'due',
                    'view' => $view, ];
            } else {
                $output = ['status' => 'paid',
                    'view' => '',
                    'msg' => __('purchase.amount_already_paid'),  ];
            }

            return json_encode($output);
        }
    }

    /**
     * Shows contact's multi-payment modal
     */
    public function createMultiPayment($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax())
        {
            $due_payment_type = request()->input('type');
            $business_id = request()->session()->get('user.business_id');
            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            if($due_payment_type == 'purchase') {
                return view('transaction_payment.create_multi_payment_purchase_modal')->with(compact('payment_types'));
            } else if($due_payment_type == 'sell') {
                return view('transaction_payment.create_multi_payment_modal')->with(compact('payment_types'));
            }
        }
    }

    public function storeMultiPayment(Request $request)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $business_id = request()->session()->get('user.business_id');
            $total_amount = request()->total_amount;
            $transaction_type = request()->transaction_type;

            $contact = Contact::findOrFail(request()->contact_id);
            $contact_balance = $contact->balance;
            if ($inputs['method'] == 'advance' && $total_amount > $contact_balance) {
                throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
            }

            $inputs = $request->only(['method', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number', 'contact_id']);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['created_by'] = auth()->user()->id;
            $inputs['payment_for'] = $contact->id;
            $inputs['business_id'] = $request->session()->get('business.id');

            // To upload document if any
            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            //Generate reference number
            $prefix_type = $transaction_type . '_payment';
            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
            $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            DB::beginTransaction();

            $pays = json_decode($request->input('pays'), true);
            $inputs['multi_pay'] = count($pays) > 1 ? true : false;
            foreach($pays as $key => $pay) {
                $transaction_id = $pay['transaction_id'];
                $pay_amount = $pay['amount'];
                if($pay_amount == 0) continue;

                $transaction = Transaction::where('business_id', $business_id)->with(['contact'])->findOrFail($transaction_id);
                $transaction_before = $transaction->replicate();

                $inputs['transaction_id'] = $transaction_id;
                $inputs['amount'] = $pay_amount;

                if ($inputs['method'] == 'custom_pay_1') {
                    $inputs['transaction_no'] = $request->input('transaction_no_1');
                } elseif ($inputs['method'] == 'custom_pay_2') {
                    $inputs['transaction_no'] = $request->input('transaction_no_2');
                } elseif ($inputs['method'] == 'custom_pay_3') {
                    $inputs['transaction_no'] = $request->input('transaction_no_3');
                }

                if (! empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                    $inputs['account_id'] = $request->input('account_id');
                }

                // To create payment
                $tp = TransactionPayment::create($inputs);

                if (! empty($request->input('denominations'))) {
                    $this->transactionUtil->addCashDenominations($tp, $request->input('denominations'));
                }

                $inputs['transaction_type'] = $transaction->type;
                event(new TransactionPaymentAdded($tp, $inputs));

                //update payment status
                $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                $transaction->payment_status = $payment_status;

                $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
                
                // Creating notification
                $transaction_data['transaction_id'] = $transaction->id;

                $current_business = Business::find($business_id);
                $data = json_decode($current_business->notification_receivers, true);
                if ($data) {
                    $receivers = isset($data['payment']) ? $data['payment'] : [];
                } else {
                    $data = [];
                    $receivers = [];
                }
                if (!in_array(auth()->user()->id, $receivers)) {
                    // $receivers[] = auth()->user()->id;
                }
                $transaction_data['receivers'] = $receivers;
                $user = User::find(auth()->user()->id);
                $user->notify(new PaymentNotification($transaction_data));
            }

            DB::commit();

            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = __('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            }

            $output = ['success' => false,
                'msg' => $msg,
            ];
        }

        return json_encode($output);
    }

    /**
     * Shows contact's payment due modal
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $business_details = $this->businessUtil->getDetails($business_id);

            $due_payment_type = request()->input('type');
            $query = Contact::where('contacts.id', $contact_id)
                            ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
            if ($due_payment_type == 'purchase') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'purchase_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'sell') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                );
            } elseif ($due_payment_type == 'sell_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            }

            //Query for opening balance details
            $query->addSelect(
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            );
            $contact_details = $query->first();

            $payment_line = new TransactionPayment();
            if ($due_payment_type == 'purchase') {
                $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
                $payment_line->amount = $contact_details->total_purchase -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'purchase_return') {
                $payment_line->amount = $contact_details->total_purchase_return -
                                    $contact_details->total_return_paid;
            } elseif ($due_payment_type == 'sell') {
                $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                $payment_line->amount = $contact_details->total_invoice -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'sell_return') {
                $payment_line->amount = $contact_details->total_sell_return -
                                    $contact_details->total_return_paid;
            }

            //If opening balance due exists add to payment amount
            $contact_details->opening_balance = ! empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
            $contact_details->opening_balance_paid = ! empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
            $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
            if ($ob_due > 0) {
                $payment_line->amount += $ob_due;
            }

            $amount_formated = $this->transactionUtil->num_f($payment_line->amount);

            $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

            $payment_line->method = 'cash';
            $payment_line->paid_on = \Carbon::now()->toDateTimeString();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

            return view('transaction_payment.pay_supplier_due_modal')
                        ->with(compact('contact_details', 'payment_types', 'payment_line', 'due_payment_type', 
                        'currencies', 'business_details', 'ob_due', 'amount_formated', 'accounts'));
        }
    }

    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPayContactDue(Request $request)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('business.id');
            $tp = $this->transactionUtil->payContact($request);

            $pos_settings = ! empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
            $enable_cash_denomination_for_payment_methods = ! empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
            //add cash denomination
            if (in_array($tp->method, $enable_cash_denomination_for_payment_methods) && ! empty($request->input('denominations')) && ! empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens') {
                $denominations = [];

                foreach ($request->input('denominations') as $key => $value) {
                    if (! empty($value)) {
                        $denominations[] = [
                            'business_id' => $business_id,
                            'amount' => $key,
                            'total_count' => $value,
                        ];
                    }
                }

                if (! empty($denominations)) {
                    $tp->denominations()->createMany($denominations);
                }
            }

            DB::commit();
            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => 'File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage(),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * view details of single..,
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewPayment($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;
            if (! empty($single_payment_line->transaction_id)) {
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                                ->with(['contact', 'location', 'transaction_for'])
                                ->first();
            } else {
                $child_payment = TransactionPayment::where('business_id', $business_id)
                        ->where('parent_id', $payment_id)
                        ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                        ->first();
                $transaction = ! empty($child_payment) ? $child_payment->transaction : null;
            }

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.single_payment_view')
                    ->with(compact('single_payment_line', 'transaction', 'payment_types'));
        }
    }

    /**
     * Retrieves all the child payments of a parent payments
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showChildPayments($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');

            $child_payments = TransactionPayment::where('business_id', $business_id)
                                                    ->where('parent_id', $payment_id)
                                                    ->with(['transaction', 'transaction.contact'])
                                                    ->get();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.show_child_payments')
                    ->with(compact('child_payments', 'payment_types'));
        }
    }

    /**
     * Retrieves list of all opening balance payments.
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getOpeningBalancePayments($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'opening_balance')
                ->where('t.contact_id', $contact_id)
                ->where('transaction_payments.business_id', $business_id)
                ->select(
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number'
                )
                ->groupBy('transaction_payments.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            return Datatables::of($query)
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.'.$row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="'.$row->amount.'" data-currency_symbol = true>'.$row->amount.'</span>';
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$id]) }}"><i class="fas fa-eye"></i> @lang("messages.view")
                    </button> <button type="button" class="btn btn-info btn-xs edit_payment" 
                    data-href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'edit\'], [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp; <button type="button" class="btn btn-danger btn-xs delete_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'destroy\'], [$id]) }}"
                    ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['amount', 'method', 'action'])
                ->make(true);
        }
    }
}
