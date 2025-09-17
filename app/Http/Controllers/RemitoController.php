<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\RemitoLine;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Warranty;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class RemitoController extends Controller
{
    protected $businessUtil;
    protected $contactUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;

    public function __construct(
        BusinessUtil $businessUtil, 
        TransactionUtil $transactionUtil, 
        ContactUtil $contactUtil, 
        ModuleUtil $moduleUtil,
        ProductUtil $productUtil
        )
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
    }

    public function index() {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        if (request()->ajax()) {
            $remitos = $this->transactionUtil->getRemitos($business_id); 

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $remitos->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->customer_id)) {
                $remitos->where('contacts.id', request()->customer_id);
            }
            if (! empty(request()->location_id)) {
                $remitos->where('transactions.location_id', request()->location_id);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $remitos->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if(! empty(request()->created_by)) {
                $remitos->where('transactions.created_by', request()->created_by);
            }

            $remitos->orderBy('transaction_date', 'desc');
            $output = Datatables::of($remitos)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">'.
                                __('messages.actions').
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="'.action([\App\Http\Controllers\RemitoController::class, 'show'], [$row->id]).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>'.__('messages.view').'</a></li>';
                    $html .= '<li><a class="'.($row->canEdit()?"":"disabled").'" href="'.action([\App\Http\Controllers\RemitoController::class, 'edit'], [$row->id]).'"><i class="fas fa-edit"></i>'.__('messages.edit').'</a></li>';
                    $html .= '<li><a class="delete-remito '.($row->canEdit()?"":"disabled").'" href="'.action([\App\Http\Controllers\RemitoController::class, 'destroy'], [$row->id]).'"><i class="fas fa-trash"></i>'.__('messages.delete').'</a></li>';
                    $html .= '</ul></div>';

                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{$transaction_date}}')
                ->addColumn('contact_name', function($row) {
                    $html = "";
                    if(!empty($row->supplier_business_name)) $html .= "<div>" . $row->supplier_business_name . "</div>";
                    if(!empty($row->name)) $html .= "<div>" . $row->name . "</div>";
                    return $html;
                })
                ->filterColumn('contact_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn('document_type', function($row) {
                    $html = '';
                    if($row->pymo_invoice) {
                        $html = "<a href='/uploads/invoices/" . $row->business_id . '/' . $row->pymo_invoice . ".pdf' class='ms-2' target='_blank'>" . $row->document_type . "</a>";
                    } else {
                        $html = "<a href='".action([\App\Http\Controllers\RemitoController::class, 'showInternalInvoice'], [$row->id])."' class='ms-2' target='_blank'>". __('invoice.internal') ."</a>";
                    }
                    return $html;
                })
                ->rawColumns(['action', 'invoice_no', 'document_type', 'contact_name'])
                ->make(true);
            return $output;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $remitos_representative = User::forDropdown($business_id, false, false, true);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('remito.index')
            ->with(compact('business_locations', 'customers', 'remitos_representative', 'sales_representative'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);
        
        // To get locations and default location
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];
        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        // default datetime
        $default_datetime = $this->businessUtil->format_date('now', true);

        // To get pos_setting
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        // To get default invoice theme
        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        if (! empty($default_location) && !empty($default_location->sale_invoice_scheme_id)) {
            $default_invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                                        ->findorfail($default_location->sale_invoice_scheme_id);
        }

        //Accounts
        $users = config('constants.enable_contact_assign') ? User::forDropdown($business_id, false, false, false, true) : [];

        return view('remito.create')->with(compact(
            'business_details',
            'walk_in_customer',
            'business_locations',
            'bl_attributes',
            'default_location',
            'default_datetime',
            'invoice_schemes',
            'default_invoice_schemes',
            'users',
            'pos_settings'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function edit($id) {
        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (! $this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days]), ]);
        }

        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);

        $transaction = Transaction::where('business_id', $business_id)
            ->where('type', 'remito')
            ->findorfail($id);

        if(!$transaction->canEdit()) {
            abort(403, 'This transaction can not be edit.');
        }
            
        $location_id = $transaction->location_id;
        
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $remito_details = RemitoLine::join('products AS p','remito_lines.product_id','=','p.id')
            ->join('variations AS variations','remito_lines.variation_id','=','variations.id')
            ->join('product_variations AS pv','variations.product_variation_id','=','pv.id')
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->leftjoin('units as u', 'p.secondary_unit_id', '=', 'u.id')
            ->where('remito_lines.transaction_id', $id)
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                'p.id as product_id',
                'p.enable_stock',
                'p.name as product_actual_name',
                'p.type as product_type',
                'pv.name as product_variation_name',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.barcode_type',
                'p.enable_sr_no',
                'variations.id as variation_id',
                'units.short_name as unit',
                'units.allow_decimal as unit_allow_decimal',
                'u.short_name as second_unit',
                'remito_lines.id as remito_line_id',
                'remito_lines.quantity as quantity_ordered',
                'units.id as unit_id',
                'remito_lines.sub_unit_id',

                //qty_available not added when negative to avoid max quanity getting decreased in edit and showing error in max quantity validation
                DB::raw('IF(vld.qty_available > 0, vld.qty_available + remito_lines.quantity, remito_lines.quantity) AS qty_available')
            )
            ->get();

        if (! empty($remito_details)) {
            foreach ($remito_details as $key => $value) {
                $remito_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available, false, null, true);

                //Add available lot numbers for dropdown to remito_lines
                $lot_numbers = [];
                if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                    $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                    foreach ($lot_number_obj as $lot_number) {
                        //If lot number is selected added ordered quantity to lot quantity available
                        if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                            $lot_number->qty_available += $value->quantity_ordered;
                        }

                        $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                        $lot_numbers[] = $lot_number;
                    }
                }
                $remito_details[$key]->lot_numbers = $lot_numbers;

                if (! empty($value->sub_unit_id)) {
                    $value = $this->productUtil->changeRemitoLineUnit($business_id, $value);
                    $remito_details[$key] = $value;
                }

                $remito_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available, false, null, true);
            }
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $customer_groups = CustomerGroup::forDropdown($business_id);

        return view('remito.edit')->with(compact(
            'business_details',
            'walk_in_customer',
            'business_locations',
            'bl_attributes',
            'transaction',
            'remito_details'
        ));
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
            $business = Business::find($business_id);

            //TODO: Check for "Undefined index: total_before_tax" issue
            $request->validate([
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'location_id' => 'required',
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);

            $transaction_data = $request->only(['invoice_no', 'contact_id', 'transaction_date', 'location_id', 'additional_notes']);
            $user_id = $request->session()->get('user.id');

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'remito';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('remito');
            //Generate reference number
            if (empty($transaction_data['invoice_no'])) {
                $transaction_data['invoice_no'] = $this->productUtil->generateReferenceNumber('remito', $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $products = $request->input('products') ?? [];

            $this->productUtil->createOrUpdateRemitoLines($transaction, $products);

            $this->transactionUtil->activityLog($transaction, 'added');

            DB::commit();
            $output = ['success' => 1,
                'msg' => __('lang_v1.remito_add_success'),
                'redirect_url' => action([\App\Http\Controllers\RemitoController::class, 'index'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function destroy($id)
    {
        if (! auth()->user()->can('sell.delete') && ! auth()->user()->can('direct_sell.delete') && ! auth()->user()->can('so.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                //Begin transaction
                DB::beginTransaction();

                $output = $this->transactionUtil->deleteRemito($business_id, $id);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output['success'] = false;
                $output['msg'] = trans('messages.something_went_wrong');
            }

            return $output;
        }
    }

    public function update(Request $request, $id) 
    {
        try {
            //TODO: Check for "Undefined index: total_before_tax" issue
            $request->validate([
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'location_id' => 'required',
                'document' => 'file|max:'.(config('constants.document_size_limit') / 1000),
            ]);

            $user_id = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');
            $business = Business::find($business_id);

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'remito')
                ->findorfail($id);

            // To get data from request
            $transaction_data = $request->only(['invoice_no', 'contact_id', 'transaction_date', 'location_id', 'additional_notes']);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            //Update reference count
            if (empty($transaction_data['invoice_no'])) {
                $ref_count = $this->productUtil->setAndGetReferenceCount('remito');
                $transaction_data['invoice_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction->created_by = $user_id;
            $transaction->transaction_date = $this->productUtil->uf_date($transaction_data['transaction_date'], true);
            $transaction->additional_notes = $transaction_data['additional_notes'];
            $transaction->invoice_no = $transaction_data['invoice_no'];
            if($transaction_data['document']) $transaction->document = $transaction_data['document'];

            DB::beginTransaction();

            // To update transaction
            $transaction->save();

            // To update remito_lines
            $products = $request->input('products') ?? [];
            $this->productUtil->createOrUpdateRemitoLines($transaction, $products);

            // To write log
            $this->transactionUtil->activityLog($transaction, 'edited');

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('lang_v1.remito_add_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);       
    }

    public function showInternalInvoice($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (! empty($transaction)) {
            $receipt = $this->receiptContent($transaction->business_id, $transaction->location_id, $transaction->id, 'browser');
            $title = $transaction->business->name.' | '.$transaction->invoice_no;
            return view('sale_pos.partials.show_invoice')->with(compact('receipt', 'title'));
        } else {
            exit(__('messages.something_went_wrong'));
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
        $printer_type = null,
    ) {
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];
        $receipt_details->currency = $currency_details;

        $output['print_title'] = $receipt_details->invoice_no;

        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $output['html_content'] = view('remito.partials.receipt', compact('receipt_details'))->render();
        }

        return $output;
    }
}