<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Product;
use App\Contact;
use App\PymoAccount;
use App\TaxRate;
use App\Business;
use App\Services\PymoService;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;

use Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;


class PymoController extends Controller
{
    protected $pymoService;
    protected $transactionUtil;

    public function __construct(PymoService $pymoService, TransactionUtil $transactionUtil)
    {
        $this->pymoService = $pymoService;
        $this->transactionUtil = $transactionUtil;
    }

    public function purchaseCfes()
    {
        if (auth()->user()) {
            $user = auth()->user();
        } else {
            abort(403, 'Unauthorized action.');
        }        

        $business_id = session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $cfeTypes = config('constants.cfe_types');
        $cfeStatus = config('constants.cfe_status');

        if (request()->ajax()) {
            $cfeType = request()->input('cfeType');
            $cfeStatus = request()->input('cfeStatus');
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $start = request()->input('start');
            $limit = request()->input('length');

            $filter = [];
            if(!empty($cfeType)) $filter["cfeType"] = $cfeType;
            if(!empty($cfeStatus)) $filter['cfeStatus'] = $cfeStatus;
            if(!empty($start_date)) $filter["start_date"] = $start_date.'T00:00:00-03:00';
            if(!empty($end_date)) $filter["end_date"] = $end_date.'T23:59:00-03:00';
            $filter["skip"] = isset($start) ? $start : 0;
            $filter["limit"] = $limit;

            $pymoInfo = PymoAccount::where('user_id', $user->id)->first();
            if (!$pymoInfo) {
                return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information'], 403);
            }
            $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
            if ($response['status'] != 'SUCCESS') {
                return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!'], 403);
            }
            
            $response = $this->pymoService->purchaseCfes($pymoInfo->rut, $filter);
            if ($response['status'] == 'SUCCESS') {
                $cfes = $response['payload']['receivedCfes'];
                $datatable = Datatables::of(collect($cfes))
                    ->addColumn('provider', function($row) use($business) {
                        return $row['data']['CFE']['eFact']['Encabezado']['Emisor']['RznSoc'];
                    })
                    ->editColumn('TipoCFE', function($row) use($cfeTypes) {
                        return array_key_exists($row['TipoCFE'], $cfeTypes) ? $cfeTypes[$row['TipoCFE']] : $row['TipoCFE'];
                    })
                    ->editColumn('TotalNeto', function($row) use($business) {
                        $data_orig = "data-orig-value";
                        if( strcasecmp($business->currency->code, $row['Moneda']) ) {
                            $data_orig = "data-orig-value-currency2";
                        }
                        return  '<span class="TotalNeto" '.$data_orig.'="' . $row['TotalNeto'] . '">'
                            . $row['TotalNeto']
                            . '</span>';
                    })
                    ->editColumn('TotalIVA', function($row) use($business) {
                        $data_orig = "data-orig-value";
                        if( strcasecmp($business->currency->code, $row['Moneda']) ) {
                            $data_orig = "data-orig-value-currency2";
                        }
                        return  '<span class="TotalIVA" '.$data_orig.'="' . $row['TotalIVA'] . '">'
                            . $row['TotalIVA']
                            . '</span>';
                    })
                    ->editColumn('MontoTotal', function($row) use($business) {
                        $data_orig = "data-orig-value";
                        if( strcasecmp($business->currency->code, $row['Moneda']) ) {
                            $data_orig = "data-orig-value-currency2";
                        }
                        return  '<span class="MontoTotal" '.$data_orig.'="' . $row['MontoTotal'] . '">'
                            . $row['MontoTotal']
                            . '</span>';
                    })
                    ->rawColumns(['provider', 'TotalNeto', 'TotalIVA', 'MontoTotal']);

                return $datatable->make(true);
            } else {
                return new JsonResponse(['status' => 'error', 'message' => 'Failed to get invoices from pymo!'], 500);
            }
        }

        return view('pymo.purchaseCfes')->with(compact('cfeTypes', 'cfeStatus', 'currencies'));
    }

    public function sentCfes(PymoService $pymoService)
    {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        $business = Business::find($business_id);
        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $cfeTypes = config('constants.cfe_types');
        $cfeStatus = config('constants.cfe_status');

        if (request()->ajax()) {
            $cfeType = request()->input('cfeType');
            $cfeStatus = request()->input('cfeStatus');
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $start = request()->input('start');
            $limit = request()->input('length');

            $filter = [];
            if(!empty($cfeType)) $filter["cfeType"] = $cfeType;
            if(!empty($cfeStatus)) $filter['cfeStatus'] = $cfeStatus;
            if(!empty($start_date)) $filter["start_date"] = $start_date.'T00:00:000Z';
            if(!empty($end_date)) $filter["end_date"] = $end_date.'T23:59:000Z';
            $filter["skip"] = isset($start) ? $start : 0;
            $filter["limit"] = $limit;

            $pymoInfo = PymoAccount::where('user_id', $userId)->first();
            if (!$pymoInfo) {
                return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
            }
            $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
            if ($response['status'] != 'SUCCESS') {
                return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
            }
            
            $response = $this->pymoService->sentCfes($pymoInfo->rut, $filter);
            if ($response['status'] == 'SUCCESS') {
                $cfes = $response['payload']['companySentCfes'];
                $pymo_invoices = [];
                foreach($cfes as $key => $cfe)
                {
                    $pymo_invoices[] = $cfe["_id"];
                }

                $transactions = $this->transactionUtil->getListByPymoInvoice($business_id, $pymo_invoices);
                $datatable = Datatables::of($transactions)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group d-flex justify-content-center" >
                                        <button type="button" class="btn btn-info btn-xs add-multi-pay" data-sell-id="'. $row->id . '">'
                                        . __('messages.add') .
                                    '</button></div>';
                            return $html;
                        }
                    )
                    ->addColumn(
                        'cfeStatus',
                        function($row) use ($cfes) {
                            $cfe = Util::array_find($cfes, function($cfe) use($row) {
                                return $cfe["_id"] == $row["pymo_invoice"];
                            });
                            if($cfe) return "<span>".$cfe["actualCfeStatus"]."</span>";
                            else return "";
                        }
                    )
                    ->editColumn('currency', function($row) use($business) {
                        return $business->isMainCurrency($row->currency_id) ? $business->currency->symbol : $business->secondCurrency->symbol;
                    })
                    ->editColumn(
                        'final_total', function($row) use ($business, $cfes) {
                            $is_credit_note = false;
                            foreach($cfes as $cfe) {
                                if($cfe['_id'] == $row['pymo_invoice']) {
                                    $is_credit_note = ($cfe['cfeType'] == '102') || ($cfe['cfeType'] === '112');
                                    break;
                                }
                            }
                            $total = $row->final_total * ($is_credit_note ? -1 : 1);
                            $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                            return  '<span class="final-total" ' . $data_orig . '="' . $total . '">'
                                    . \App\Utils\Util::format_currency($total, $row->currency, false)
                                    . '</span>';
                        }
                    )
                    ->editColumn(
                        'tax_amount', function($row) use($business, $cfes) {
                            $is_credit_note = false;
                            foreach($cfes as $cfe) {
                                if($cfe['_id'] == $row['pymo_invoice']) {
                                    $is_credit_note = ($cfe['cfeType'] == '102') || ($cfe['cfeType'] === '112');
                                    break;
                                }
                            }
                            $tax_amount = $row->tax_amount * ($is_credit_note ? -1 : 1);
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
                        'total_paid', function($row) use ($business, $cfes) {
                            $is_credit_note = false;
                            foreach($cfes as $cfe) {
                                if($cfe['_id'] == $row['pymo_invoice']) {
                                    $is_credit_note = ($cfe['cfeType'] == '102') || ($cfe['cfeType'] === '112');
                                    break;
                                }
                            }
                            $total_paid = $row->total_paid * ($is_credit_note ? -1 : 1);
                            $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                            return  '<span class="total-paid" ' . $data_orig . '="' . $total_paid . '" > ' 
                                    . \App\Utils\Util::format_currency($total_paid, $row->currency, false)
                                    . '</span>';
                        }
                    )
                    ->editColumn(
                        'total_before_tax', function($row) use($business, $cfes) {
                            $is_credit_note = false;
                            foreach($cfes as $cfe) {
                                if($cfe['_id'] == $row['pymo_invoice']) {
                                    $is_credit_note = ($cfe['cfeType'] == '102') || ($cfe['cfeType'] === '112');
                                    break;
                                }
                            }
                            $total_before_tax = $row->total_before_tax * ($is_credit_note ? -1 : 1);
                            $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                            return  '<span class="total-before-tax" ' . $data_orig . '="' . $total_before_tax . '" > '
                                    . \App\Utils\Util::format_currency($total_before_tax, $row->currency, false)
                                    . '</span>';
                        }
                    )
                    ->editColumn(
                        'discount_amount',function ($row) use ($business) {
                            $data_orig = $business->isMainCurrency($row->currency_id) ? "data-orig-value" : "data-orig-value-currency2";
                            $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;
                            if (! empty($discount) && $row->discount_type == 'percentage') {
                                $discount = $row->total_before_tax * ($discount / 100);
                            }
                            return  '<span class="discount-amount" ' . $data_orig .'="' . $discount . '" > '
                                    . \App\Utils\Util::format_currency($discount, $row->currency, false)
                                    . '</span>';
                        }
                    )
                    ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                    ->editColumn(
                        'payment_status',
                        function ($row) {
                            $payment_status = Transaction::getPaymentStatus($row);
                            return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                        }
                    )
                    ->editColumn('invoice_no', function ($row) use ($is_crm) {
                        if($row->pymo_serie) $invoice_no = $row->pymo_serie;
                        else $invoice_no = $row->invoice_no;
                        if (! empty($row->woocommerce_order_id)) {
                            $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="'.__('lang_v1.synced_from_woocommerce').'"></i>';
                        }
                        if (! empty($row->return_exists)) {
                            $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="'.__('lang_v1.some_qty_returned_from_sell').'"><i class="fas fa-undo"></i></small>';
                        }
                        if (! empty($row->is_recurring)) {
                            $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="'.__('lang_v1.subscribed_invoice').'"><i class="fas fa-recycle"></i></small>';
                        }
    
                        if (! empty($row->recur_parent_id)) {
                            $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="'.__('lang_v1.subscription_invoice').'"><i class="fas fa-recycle"></i></small>';
                        }
    
                        if (! empty($row->is_export)) {
                            $invoice_no .= '</br><small class="label label-default no-print" title="'.__('lang_v1.export').'">'.__('lang_v1.export').'</small>';
                        }
    
                        if ($is_crm && ! empty($row->crm_is_order_request)) {
                            $invoice_no .= ' &nbsp;<small class="label bg-yellow label-round no-print" title="'.__('crm::lang.order_request').'"><i class="fas fa-tasks"></i></small>';
                        }
    
                        return $invoice_no;
                    })
                    ->addColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                    ->editColumn('document_type', function($row) {
                        $html = '';
                        if($row->pymo_invoice) {
                            $html = "<a href='/uploads/invoices/" . $row->business_id . '/' . $row->pymo_invoice . ".pdf' class='ms-2' target='_blank'>" . $row->document_type . "</a>";
                        } else {
                            $html = "<a href='/invoice/transaction/" . $row->id . "' class='ms-2' target='_blank'>". __('invoice.internal') ."</a>";
                        }
                        return $html;
                    })
                    ->rawColumns([
                        'final_total','action', 'total_paid', 'document_type',
                        'payment_status', 'invoice_no', 'discount_amount', 'cfeStatus', 'sell_return_pymo_serie',
                        'tax_amount', 'total_before_tax', 'contact_name']);

                return $datatable->make(true);
            } else {
                return new JsonResponse(['status' => 'error', 'message' => 'Failed to get invoices from pymo!']);
            }
        }
        
        return view('pymo.sentCfes')->with(compact('cfeTypes', 'cfeStatus', 'currencies'));
    }

    public function save(Request $request)
    {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $rut = $request->input('rut');
        $email = $request->input('email');
        $password = $request->input('password');
        $pdf_format = $request->input('pdf_format');
     
        try {
            $exist = PymoAccount::where('user_id', $userId)->first();
            if ($exist) {
                $pymo = $exist;
            } else {
                $pymo = new PymoAccount();
            }
            $pymo->email = $email;
            $pymo->rut = $rut;
            $pymo->user_id = $userId;
            $pymo->pdf_format = $pdf_format;
            if(!empty($password)) $pymo->password = $password;
            
            $response = $this->pymoService->login($pymo->email, $pymo->password);
            if ($response['status'] == 'SUCCESS') {
                $response = $this->pymoService->getCompany($rut);
                if ($response['status'] == 'SUCCESS') {
                    $pymo->room = $response['payload']['company']['branchOffices'][0]['number'];
                    $pymo->save();
                    return new JsonResponse(['status' => 'ok', 'data' => $response]);
                } else {
                    return new JsonResponse(['status' => 'false', 'message' => 'Invalid RUT']);
                }
            } else {
                return new JsonResponse(['status' => 'false', 'message' => 'Invalid Pymo Account']);
            }
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return new JsonResponse(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        // Call the login method from the PymoService
        $email = "motion1@soft.com";
        $password = "motion";
        $response = $this->pymoService->login($email, $password);
        if ($response['status'] == 'SUCCESS') {
            $response = $this->pymoService->getCompany(216753930012);
            return $response;
        }
        return $response;
    }

    public function generateTicketFormatInvoice($cfeType, $invoice_data, $qrUrl)
    {
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $qrUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        
        $result = $writer->write($qrCode);
        $qrcodeUri = $result->getDataUri();

        // To generate pdf
        $pdf = PDF::loadView('pymo.ticket_pdf', compact('cfeType', 'invoice_data', 'qrcodeUri'));
        $pdf->setPaper([0, 0, 265, 1100]);

        $pdfContent = $pdf->output();
        return $pdfContent;
    }

    public function sendInvoice(Request $request)
    {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $transaction = Transaction::with(['currency', 'business'])->find($request->transaction_id);
        if(!$transaction) return new JsonResponse(['status' => 'error', 'message' => 'No found transaction!']);

        $data = $request;
        $item_array = [];
        $globalTaxInfo = TaxRate::find($data['tax_rate_id']);

        if($data['discount_type'] == 'fixed') return new JsonResponse(['status' => 'error', 'message' => 'Pymo does not support fixed_discount!']);
        $discountPercent = ($data['discount_type'] == 'percentage') ? $data['discount_amount'] : 0 ;

        foreach($data['products'] as $key => $product) {
            $taxInfo = TaxRate::find($product['tax_id']);
            if ($taxInfo) {
                $taxAmount = intval($taxInfo['amount']);
            } else if ($globalTaxInfo) {
                $taxAmount = intval($globalTaxInfo['amount']);
            } else {
                $taxAmount = 0;
            }
            if ($taxAmount == 10) {
                $IndFact = 2;
            } else if ($taxAmount == 22) {
                $IndFact = 3;            
            } else {
                $IndFact = 1;
            }
            $productInfo = Product::with(['unit'])->find($product['product_id']);

            $unitPrice = str_replace(',', '', $product['unit_price_inc_tax']);
            // $unitPriceIncTax += $unitPriceIncTax * $taxAmount / 100;

            $item = [
                "SubDescuento"=> [],
                "SubRecargo"=> [],
                "RetencPercep"=> [],
                "NroLinDet"=> ($key+1),             // Line number or sequential number that identifies the item on the invoice   
                "IndFact" => $IndFact,              // Billing indicator   
                "NomItem" => $productInfo->name,    // Name of the product or service
                // "DscItem" => $productInfo->product_description, // item description Up to 1000 characters.
                "Cantidad" => $product['quantity'], // qty
                "UniMed" => $productInfo->unit->actual_name, // Unit of measurement of the Quantity field. If not applicable, enter "N/A".
                "PrecioUnitario" => $unitPrice, // Unit price for the Item. Printing is not necessary to provide services.
                // "MontoItem" => $unitPriceIncTax * $product['quantity'], // Value per detail line. Note: If not specified, it is calculated as Quantity*UnitPrice (+ tax if "GrossMnt" is defined).
                "DescuentoPct" => $discountPercent, // Discount in % that will be applied to each line.
                // "RecargoPct" =>   10, // Surcharge in % that will be applied to each line.
            ];
            $item_array[] = $item;
        }
        // get receipter info
        $contactInfo = Contact::find($request['contact_id']);

        $invoice_data = [
            "emailsToNotify" => ($contactInfo->email ? [$contactInfo->email] : [])
        ];
        
        if (!$contactInfo->rut) {
            return new JsonResponse(['status' => 'error', 'message' => 'Receiptor\'s RUT can not be null!']);
        }

        // To log in pymo
        $pymoInfo = PymoAccount::where('user_id', $userId)->first();
        if (!$pymoInfo) {
            return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
        }
        $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
        if ($response['status'] != 'SUCCESS') {
            return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
        }
        // To get company Cfe
        $companyCfesActiveNumbers = $this->pymoService->getCompanyCfesActiveNumbers($pymoInfo->rut);

        $isCashDocument = $request['final_total'] <= $request['payment'][0]['amount'] ? 1 : 2;  // 1: cache 2:credit
        $document_type = '';
        $cfeType = '';

        if ($contactInfo->contact_type_radio == 'business') {
            $cfeType = '111';
            $document_type = $isCashDocument == 1 ? __('invoice.e_invoice_cash') : __('invoice.e_invoice_credit');

            $invoice_data["111"] = [
                [
                    "adenda" => $request['sale_note'],
                    "clientEmissionId" => $request['invoice_no'], // invoice number
                    "Receptor" => [
                        "TipoDocRecep" => "2", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->supplier_business_name ?? $contactInfo->name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales" => [
                        "TpoMoneda" => $transaction->currency->code,
                        "TpoCambio" => ($transaction->currency->code == 'UYU') ?  '1' : $request['nation_exchg_rate']
                    ],
                    "Items" => $item_array
                ]
            ];

            if(!empty($companyCfesActiveNumbers) && !empty($companyCfesActiveNumbers[0]['CAEEspecial']) && $companyCfesActiveNumbers[0]['CAEEspecial']=='2') {
                $invoice_data["111"][0]['IdDoc'] = [
                    "FmaPago" => $isCashDocument,
                    "MntBruto" => 3,
                    "CAEEspecial" => "2"
                ];
            } else {
                $invoice_data["111"][0]['IdDoc'] = [
                    "FmaPago" => $isCashDocument,
                ];
            }
        } else {
            $cfeType = '101';
            $document_type = $isCashDocument == 1 ? __('invoice.e_ticket_cash') : __('invoice.e_ticket_credit');

            $invoice_data["101"] = [
                [
                    "adenda"=> $request['sale_note'],
                    "clientEmissionId" => $request['invoice_no'], // invoice number
                    "Receptor" => [
                        "TipoDocRecep" => "3", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->name ? $contactInfo->name : $contactInfo->supplier_business_name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales"=> [
                        "TpoMoneda" => $transaction->currency->code,
                        "TpoCambio" => ($transaction->currency->code == 'UYU') ?  '1' : $request['nation_exchg_rate']
                    ],
                    "Items"=> $item_array
                ]
            ];
            if(!empty($companyCfesActiveNumbers) && !empty($companyCfesActiveNumbers[0]['CAEEspecial']) && $companyCfesActiveNumbers[0]['CAEEspecial']=='2') {
                $invoice_data["101"][0]['IdDoc'] = [
                    "FmaPago" => $isCashDocument,
                    "MntBruto" => 3,
                    "CAEEspecial" => "2"
                ];
            } else {
                $invoice_data["101"][0]['IdDoc'] = [
                    "FmaPago" => $isCashDocument,
                ];
            }
        }

        // To send invoice
        $response = $this->pymoService->sendInvoice($pymoInfo->rut, $pymoInfo->room, $invoice_data);
        if ($response['status'] == 'SUCCESS') {
            $cfeIds = $response['payload']['cfesIds'];
            if (isset($cfeIds[0]['status']) && $cfeIds[0]['status'] == 'FAIL') {
                return new JsonResponse(['status' => 'error', 'data' => $cfeIds[0]]);
            } else {
                $response['invoice_id'] = $cfeIds[0]['id'];
                \Log::info('received invoice_id from pymo: invoice_id=' . $response['invoice_id']);

                // To get pdf file
                if($pymoInfo->pdf_format == 'ticket_format') {
                    $cfe_detail_response = $this->pymoService->getCfeDetail($pymoInfo->rut, $cfeType, $cfeIds[0]['serie'], $cfeIds[0]['nro']);
                    if($cfe_detail_response['status'] == 'ok') {
                        $invoice_pdfdata = $this->generateTicketFormatInvoice($cfeType, $cfe_detail_response['data'], $cfeIds[0]['qrUrl']);
                    }
                } else {
                    $invoice_pdfdata = $this->pymoService->getInvoice($pymoInfo->rut, $response['invoice_id']);
                }
    
                if(!empty($invoice_pdfdata)) {
                    $folderRelativePath = 'uploads/invoices/'.$transaction->business->id;
                    $folderPath = public_path($folderRelativePath);
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
        
                    $filePath = public_path($folderRelativePath.'/'.$response['invoice_id'].'.pdf');
                    $result = file_put_contents($filePath, $invoice_pdfdata);
                }

                // To update transaction 
                \Log::info('updating transaction: pymo_invoice=' . $response['invoice_id']);
                $pymo_serie = $cfeIds[0]['serie'] . $cfeIds[0]['nro'];
                $transaction->pymo_invoice = $response['invoice_id'];
                $transaction->pymo_serie = $pymo_serie;
                $transaction->document_type = $document_type;
                \Log::info('updating transaction: pymo_invoice=' . $transaction->pymo_invoice . ', transactino_id=' . $transaction->id);

                $transaction->save();

                if(empty($invoice_pdfdata)) {
                    return new JsonResponse(['status' => 'error', 'message' => 'Invoice issued, but failed to create pdf file!']);
                } 

                return $response;
            }
        } else {
            return new JsonResponse(['status' => 'error', 'message' => 'Failed to send Invoice!']);
        }
    }

    public function sendRemitoInvoice(Request $request)
    {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $transaction = Transaction::with(['currency', 'business'])->find($request->transaction_id);
        if(!$transaction) return new JsonResponse(['status' => 'error', 'message' => 'No found transaction!']);

        $data = $request;
        $item_array = [];

        foreach($data['products'] as $key => $product) {
            $productInfo = Product::with(['unit'])->find($product['product_id']);
            $item = [
                "NroLinDet"=> ($key+1),             // Line number or sequential number that identifies the item on the invoice   
                "IndFact" => 8,
                "NomItem" => $productInfo->name,    // Name of the product or service
                "Cantidad" => $product['quantity'], // qty
                "UniMed" => $productInfo->unit->actual_name, // Unit of measurement of the Quantity field. If not applicable, enter "N/A".
            ];
            $item_array[] = $item;
        }
        // get receipter info
        $contactInfo = Contact::findOrFail($request['contact_id']);

        $invoice_data = [
            "emailsToNotify" => ($contactInfo->email ? [$contactInfo->email] : [])
        ];
        
        if (!$contactInfo->rut) {
            return new JsonResponse(['status' => 'error', 'message' => 'Receiptor\'s RUT can not be null!']);
        }

        if ($contactInfo->contact_type_radio == 'business') {
            $cfeType = '181';
            $document_type = 'Remito';

            $invoice_data["181"] = [
                [
                    "adenda" => $request['additional_notes'],
                    "clientEmissionId" => $request['invoice_no'], // invoice number
                    "IdDoc" => [
                        "TipoTraslado" => "1",
                    ],
                    "Receptor" => [
                        "TipoDocRecep" => "2", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->supplier_business_name ?? $contactInfo->name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales" => [
                        "CantLinDet" => '1',
                    ],
                    "Items" => $item_array
                ]
            ];
        } else {
            $cfeType = '281';
            $document_type = 'Remito';

            $invoice_data["281"] = [
                [
                    "adenda" => $request['additional_notes'],
                    "clientEmissionId" => $request['invoice_no'], // invoice number
                    "IdDoc" => [
                        "TipoTraslado" => "1",
                    ],
                    "Receptor" => [
                        "TipoDocRecep" => "3", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->name ?? $contactInfo->supplier_business_name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales" => [
                        "CantLinDet" => '1',
                    ],
                    "Items" => $item_array
                ]
            ];
        }
        $pymoInfo = PymoAccount::where('user_id', $userId)->first();
        if (!$pymoInfo) {
            return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
        }
        $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
        if ($response['status'] != 'SUCCESS') {
            return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
        }
        $response = $this->pymoService->sendInvoice($pymoInfo->rut, $pymoInfo->room, $invoice_data);
        if ($response['status'] == 'SUCCESS') {
            $cfeIds = $response['payload']['cfesIds'];
            if (isset($cfeIds[0]['status']) && $cfeIds[0]['status'] == 'FAIL') {
                return new JsonResponse(['status' => 'error', 'data' => $cfeIds[0]]);
            } else {
                $response['invoice_id'] = $cfeIds[0]['id'];
                \Log::info('received invoice_id from pymo: invoice_id=' . $response['invoice_id']);

                // To get pdf file
                if($cfeType == '181' && $pymoInfo->pdf_format == 'ticket_format') {
                    $cfe_detail_response = $this->pymoService->getCfeDetail($pymoInfo->rut, $cfeType, $cfeIds[0]['serie'], $cfeIds[0]['nro']);
                    if($cfe_detail_response['status'] == 'ok') {
                        $invoice_pdfdata = $this->generateTicketFormatInvoice($cfeType, $cfe_detail_response['data'], $cfeIds[0]['qrUrl']);
                    }
                } else {
                    $invoice_pdfdata = $this->pymoService->getInvoice($pymoInfo->rut, $response['invoice_id']);
                }
    
                if(!empty($invoice_pdfdata)) {
                    $folderRelativePath = 'uploads/invoices/'.$transaction->business->id;
                    $folderPath = public_path($folderRelativePath);
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
        
                    $filePath = public_path($folderRelativePath.'/'.$response['invoice_id'].'.pdf');
                    $result = file_put_contents($filePath, $invoice_pdfdata);
                }

                // To update transaction 
                \Log::info('updating transaction: pymo_invoice=' . $response['invoice_id']);
                $pymo_serie = $cfeIds[0]['serie'] . $cfeIds[0]['nro'];
                $transaction->pymo_invoice = $response['invoice_id'];
                $transaction->pymo_serie = $pymo_serie;
                $transaction->document_type = $document_type;
                \Log::info('updating transaction: pymo_invoice=' . $transaction->pymo_invoice . ', transactino_id=' . $transaction->id);
                
                $transaction->save();

                if(empty($invoice_pdfdata)) {
                    return new JsonResponse(['status' => 'error', 'message' => 'Invoice issued, but failed to create pdf file!']);
                } 
                
                return $response;
            }
        } else {
            return new JsonResponse(['status' => 'error', 'message' => 'Failed to send Invoice!']);
        }
    }

    public function sendReturnInvoice(Request $request)
    {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $transaction = Transaction::with(['currency', 'tax'])->find($request->transaction_id);
        if(!$transaction) return new JsonResponse(['status' => 'error', 'message' => 'No found transaction!']);

        $sell_return = Transaction::find($request->sell_return_id);
        if(!$sell_return) return new JsonResponse(['status' => 'error', 'message' => 'No found return transaction!']);

        $pymo_seria= '';
        $pymo_nro = '';
        if(!empty($transaction->pymo_serie)) {
            preg_match('/[A-Za-z]+/', $transaction->pymo_serie, $serias);
            $pymo_seria = $serias[0] ?? '';
            preg_match('/\d+/', $transaction->pymo_serie, $nros);
            $pymo_nro = $nros[0] ?? '';
        }

        $data = $request;
        $item_array = [];

        if($data['discount_type'] == 'fixed') return new JsonResponse(['status' => 'error', 'message' => 'No found transaction!']);
        $discountPercent = ($data['discount_type'] == 'percentage') ? $data['discount_amount'] : 0 ;

        foreach($data['products'] as $key => $product) {
            $IndFact = 1;
            $taxInfo = TaxRate::find($product['tax_id']);
            if ($taxInfo) {
                $taxAmount = intval($taxInfo['amount']);
            } else if ($transaction->tax) {
                $taxAmount = intval($transaction->tax['amount']);
            } else {
                $taxAmount = 0;
            }
            
            if ($taxAmount == 10) {
                $IndFact = 2;
            } else if ($taxAmount == 22) {
                $IndFact = 3;            
            } else {
                $IndFact = 1;
            }

            $productInfo = Product::with(['unit'])->find($product['id']);
            
            $unitPrice = str_replace(',', '', $product['unit_price_inc_tax']);
            // if($transaction->tax) $unitPriceIncTax += $unitPriceIncTax * $transaction->tax->amount / 100;

            $item = [
                "SubDescuento"=> [],
                "SubRecargo"=> [],
                "RetencPercep"=> [],
                "NroLinDet"=> ($key + 1),
                "IndFact" => $IndFact,
                "NomItem" => $productInfo->name, // Name of the product or service
                // "DscItem" => $productInfo->product_description, // item description Up to 1000 characters.
                "Cantidad" => $product['quantity'], // qty
                "UniMed" => $productInfo->unit->actual_name, // Unit of measurement of the Quantity field. If not applicable, enter "N/A".
                "PrecioUnitario" => $unitPrice, // Unit price for the Item. Printing is not necessary to provide services.
                // "MontoItem" =>  "420", // Value per detail line. Note: If not specified, it is calculated as Quantity*UnitPrice (+ tax if "GrossMnt" is defined).
                "DescuentoPct" => $discountPercent, // Discount in % that will be applied to each line.
                // "RecargoPct" =>   10, // Surcharge in % that will be applied to each line.
            ];
            $item_array[] = $item;
        }
        // get receipter info
        $contactInfo = Contact::find($data['contact_id']);

        $invoice_data = [
            "emailsToNotify" => ($contactInfo->email ? [$contactInfo->email] : [])
        ];
        
        if (!$contactInfo->rut) {
            return new JsonResponse(['status' => 'error', 'message' => 'Receiptor\'s RUT can not be null!']);
        }

        $document_type = '';
        if ($contactInfo->contact_type_radio == 'business') {
            $cfeType = '112';
            $document_type = __('invoice.e_invoice_credit_note');
            $invoice_data["112"] = [ // NK eFactura
                [
                    // "adenda"=> $request['sale_note'],
                    "clientEmissionId" => $request['invoice_no'], // invoice number
                    "IdDoc"=> [
                        // "MntBruto"=> "1" ,
                        "FmaPago"=>  "1"
                    ],
                    "Receptor"=> [
                        "TipoDocRecep" => "2", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->supplier_business_name ?? $contactInfo->name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                    ],
                    "Totales" => [
                        "TpoMoneda" => $transaction->currency->code,
                        "TpoCambio" => ($transaction->currency->code == 'UYU') ?  '1' : $transaction->nation_exchg_rate
                    ],
                    "Items"=> $item_array,
                    "Referencia"=> [
                        [  
                            "NroLinRef"=> "1",
                            "TpoDocRef"=>"111",
                            "Serie"=> $pymo_seria,
                            "NroCFERef"=> $pymo_nro
                        ]
                    ]
                ]
            ];
        } else { // NK eTicket
            $cfeType = '102';
            $document_type = __('invoice.e_ticket_credit_note');
            $invoice_data["102"] = [
                [ 
                    // "adenda"=> $request['sale_note'],
                    "clientEmissionId" => $request['invoice_no'],     // invoice number
                    "IdDoc"=> [ 
                        // "MntBruto"=>  "1" ,
                        "FmaPago"=>  "1"
                    ],
                    "Receptor"=> [  
                        "TipoDocRecep"=>  "3" ,
                        "CodPaisRecep"=>  "UY" ,
                        "DocRecep"=>  $contactInfo->rut,
                        "RznSocRecep"=> $contactInfo->name ? $contactInfo->name : $contactInfo->supplier_business_name, 
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                    ],
                    "Totales"=> [ 
                        "TpoMoneda" => $transaction->currency->code,
                        "TpoCambio" => ($transaction->currency->code == 'UYU') ?  '1' : $transaction->nation_exchg_rate
                    ],
                    "Items"=> $item_array,
                    "Referencia"=> [
                        [  
                            "NroLinRef"=> "1",
                            "TpoDocRef"=>"101",
                            "Serie"=> $pymo_seria,
                            "NroCFERef"=> $pymo_nro
                        ]
                    ]
                ]
            ];
        }
        
        $pymoInfo = PymoAccount::where('user_id', $userId)->first();
        if (!$pymoInfo) {
            return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
        }
        $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
        if ($response['status'] != 'SUCCESS') {
            return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
        }

        $response = $this->pymoService->sendInvoice($pymoInfo->rut, $pymoInfo->room, $invoice_data);
        if ($response['status'] == 'SUCCESS') {
            $cfeIds = $response['payload']['cfesIds'];
            if (isset($cfeIds[0]['status']) && $cfeIds[0]['status'] == 'FAIL') {
                return new JsonResponse(['status' => 'error', 'data' => $cfeIds[0]]);
            } else {
                $response['invoice_id'] = $response['payload']['cfesIds'][0]['id'];

                if($pymoInfo->pdf_format == 'ticket_format') {
                    $cfe_detail_response = $this->pymoService->getCfeDetail($pymoInfo->rut, $cfeType, $cfeIds[0]['serie'], $cfeIds[0]['nro']);
                    if($cfe_detail_response['status'] == 'ok') {
                        $invoice_pdfdata = $this->generateTicketFormatInvoice($cfeType, $cfe_detail_response['data'], $cfeIds[0]['qrUrl']);
                    }
                } else {
                    $invoice_pdfdata = $this->pymoService->getInvoice($pymoInfo->rut, $response['invoice_id']);
                }
    
                if(!empty($invoice_pdfdata)) {
                    $folderRelativePath = 'uploads/invoices/'.$transaction->business_id;
                    $folderPath = public_path($folderRelativePath);
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
        
                    $filePath = public_path($folderRelativePath.'/'.$response['invoice_id'].'.pdf');
                    $result = file_put_contents($filePath, $invoice_pdfdata);
                }

                $pymo_serie = $cfeIds[0]['serie'] . $cfeIds[0]['nro'];
                $sell_return->pymo_invoice = $response['invoice_id'];
                $sell_return->pymo_serie = $pymo_serie;
                $sell_return->document_type = $document_type;
                $sell_return->save();

                if(empty($invoice_pdfdata)) {
                    return new JsonResponse(['status' => 'error', 'message' => 'Invoice issued, but failed to create pdf file!']);
                } 
            }
        }

        return $response;
    }

    public function sendReceiptInvoice(Request $request) {
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $contactInfo = null;
        $currency_code = null;
        $exchange_rate = 0;
        $item_array = [];
        $referencias = [];
        $payments = [];

        //
        $pays = [];
        if(isset($request->is_multi_pay) && $request->is_multi_pay == 1) {
            $pays = $request->pays;
        } else {
            $pays[] = [
                'payment_id' => $request->payment_id,
                'amount' => $request->amount
            ];
        }
        foreach($pays as $key => $pay) {
            $payment = TransactionPayment::find($pay['payment_id']);
            if(!$payment) return new JsonResponse(['status' => 'error', 'message' => 'No found payment!']);

            $payments[] = $payment;

            if(!$contactInfo) $contactInfo = Contact::findOrFail($payment->payment_for);

            $amount = $this->transactionUtil->num_uf($pay['amount']);
    
            if($payment->transaction_id) {
                $transaction = Transaction::with(['currency', 'sell_lines'])->find($payment->transaction_id);

                // To build items
                if($amount == (float)$transaction->total_before_tax) $ratio = 1;
                else $ratio = number_format($amount / $transaction->total_before_tax, 5);
                
                foreach($transaction->sell_lines as $key => $sell_line) {
                    $productInfo = Product::with(['unit'])->find($sell_line->product_id);
                    $unitPriceIncTax = str_replace(',', '', $sell_line->unit_price_inc_tax);
                    $unitPriceIncTax = (float)$unitPriceIncTax * $ratio;
        
                    $item = [
                        "SubDescuento"=> [],
                        "SubRecargo"=> [],
                        "RetencPercep"=> [],
                        "NroLinDet"=> ($key + 1),
                        "IndFact" => 6,
                        "NomItem" => $productInfo->name, // Name of the product or service
                        "Cantidad" => $sell_line->quantity, // qty
                        "UniMed" => $productInfo->unit->actual_name, // Unit of measurement of the Quantity field. If not applicable, enter "N/A".
                        "PrecioUnitario" => $unitPriceIncTax, // Unit price for the Item. Printing is not necessary to provide services.
                        // "MontoItem" => $unitPriceIncTax * $sell_line->quantity, // Value per detail line. Note: If not specified, it is calculated as Quantity*UnitPrice (+ tax if "GrossMnt" is defined).
                        // "DescuentoPct" => 10, // Discount in % that will be applied to each line.
                        // "RecargoPct" =>   10, // Surcharge in % that will be applied to each line.
                    ];
                    $item_array[] = $item;
                }

                // To build referencias
                if($transaction->pymo_serie) {
                    preg_match('/[A-Za-z]+/', $transaction->pymo_serie, $serias);
                    $seria = $serias[0] ?? '';
                    preg_match('/\d+/', $transaction->pymo_serie, $nros);
                    $nro = $nros[0] ?? '';
                    $referencias[] = [
                        "NroLinRef" => count($referencias) + 1,
                        "TpoDocRef" => ($contactInfo->contact_type_radio == 'business' ? '111' : '101'),
                        "Serie"=> $seria,
                        "NroCFERef"=> $nro
                    ];
                }

                // To get currency info
                if(!$currency_code) {
                    $currency_code = $transaction->currency?->code ?? 'UYU';
                    $exchange_rate = $transaction->currency ? ($transaction->currency->code == 'UYU' ? 1 : $transaction->nation_exchg_rate) : $transaction->exchange_rate;
                }
            } else {
                $item = [
                    "SubDescuento"=> [],
                    "SubRecargo"=> [],
                    "RetencPercep"=> [],
                    "NroLinDet"=> ($key + 1),
                    "IndFact" => 6,
                    "NomItem" => "pago a cuenta", // Name of the product or service
                    "Cantidad" => 1, // qty
                    "UniMed" => "N/A", // Unit of measurement of the Quantity field. If not applicable, enter "N/A".
                    "PrecioUnitario" => $amount, // Unit price for the Item. Printing is not necessary to provide services.
                    // "MontoItem" => $unitPriceIncTax * $sell_line->quantity, // Value per detail line. Note: If not specified, it is calculated as Quantity*UnitPrice (+ tax if "GrossMnt" is defined).
                    // "DescuentoPct" => 10, // Discount in % that will be applied to each line.
                    // "RecargoPct" =>   10, // Surcharge in % that will be applied to each line.
                ];
                $item_array[] = $item;
    
                // To get currency info
                if(!$currency_code) {
                    $currency = Currency::find($request->input('currency_id'));
                    $currency_code = $currency->code;
                    $exchange_rate = $currency_code == 'UYU' ? 1 : $request->input('nation_exchg_rate');
                }
            }
        }

        $invoice_data = [
            "emailsToNotify" => ($contactInfo->email ? [$contactInfo->email] : [])
        ];

        if (!$contactInfo->rut) {
            return new JsonResponse(['status' => 'error', 'message' => 'Receiptor\'s RUT can not be null!']);
        }

        if ($contactInfo->contact_type_radio == 'business') {
            $cfeType = '911';
            $invoice_data["111"] = [
                [
                    "adenda" => $request['note'],
                    "clientEmissionId" => $payment->payment_ref_no, // invoice number
                    "IdDoc" => [
                        // "MntBruto" => "1",
                        "FmaPago" => "1",
                        "IndCobPropia" => "1"
                    ],
                    "Receptor" => [
                        "TipoDocRecep" => "2", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->supplier_business_name ?? $contactInfo->name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales" => [
                        "TpoMoneda" => $currency_code,
                        "TpoCambio" => $exchange_rate
                    ],
                    "Items" => $item_array,
                    "Referencia" => $referencias
                ]
            ];
        } else {
            $cfeType = '901';
            $invoice_data["101"] = [
                [
                    "adenda"=> $request['note'],
                    "clientEmissionId" => $payment->payment_ref_no, // invoice number
                    "IdDoc"=> [
                        // "MntBruto"=> "1",
                        "FmaPago"=> "1",
                        "IndCobPropia" => '1'
                    ],
                    "Receptor" => [
                        "TipoDocRecep" => "3", // 2:RUC; 3:CI; 4:Others; 5:Passport; 6: DNI (Arg, Brazil, Chile or Paraguay); 7:NIFE: foreign tax identification number
                        "CodPaisRecep" => "UY", // Two-letter country code Ex: "UY"
                        "DocRecep" => $contactInfo->rut, // Document number according to the type of document RUT(2), CI(3)
                        "RznSocRecep" => $contactInfo->name ? $contactInfo->name : $contactInfo->supplier_business_name, // Name (if it is a person) or Company name of the recipient (if it is a company)
                        "DirRecep" => $contactInfo['address_line_1'] ?? 'indefinite', // Address of the recipient
                        // "CiudadRecep" => "Montevideo", // City of the recipient,
                        // "DeptoRecep" => "Montevideo", // Receiver's department
                        // "CompraID" => "" // [OPTIONAL] Number that identifies the purchase: order number, purchase order number, etc. - Alphanumeric, up to 50 chars
                    ],
                    "Totales"=> [
                        "TpoMoneda" => $currency_code,
                        "TpoCambio" => $exchange_rate
                    ],
                    "Items"=> $item_array,
                    "Referencia" => $referencias
                ]
            ];
        }
        $pymoInfo = PymoAccount::where('user_id', $userId)->first();
        if (!$pymoInfo) {
            return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
        }
        $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
        if ($response['status'] != 'SUCCESS') {
            return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
        }
        $response = $this->pymoService->sendInvoice($pymoInfo->rut, $pymoInfo->room, $invoice_data);
        if ($response['status'] == 'SUCCESS') {
            $cfeIds = $response['payload']['cfesIds'];
            if (isset($cfeIds[0]['status']) && $cfeIds[0]['status'] == 'FAIL') {
                return new JsonResponse(['status' => 'error', 'data' => $cfeIds[0]]);
            } else {
                $response['invoice_id'] = $cfeIds[0]['id'];

                if($pymoInfo->pdf_format == 'ticket_format') {
                    $cfe_detail_response = $this->pymoService->getCfeDetail($pymoInfo->rut, $cfeType, $cfeIds[0]['serie'], $cfeIds[0]['nro']);
                    if($cfe_detail_response['status'] == 'ok') {
                        $invoice_pdfdata = $this->generateTicketFormatInvoice($cfeType, $cfe_detail_response['data'], $cfeIds[0]['qrUrl']);
                    }
                } else {
                    $invoice_pdfdata = $this->pymoService->getInvoice($pymoInfo->rut, $response['invoice_id']);
                }

                if(!empty($invoice_pdfdata)) {
                    $folderRelativePath = 'uploads/invoices/'.$payment->business_id;
                    $folderPath = public_path($folderRelativePath);
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
        
                    $filePath = public_path($folderRelativePath.'/'.$response['invoice_id'].'.pdf');
                    $result = file_put_contents($filePath, $invoice_pdfdata);
                }

                $pymo_serie = $cfeIds[0]['serie'] . $cfeIds[0]['nro'];

                foreach($payments as $key => $payment) {
                    $payment->pymo_invoice = $response['invoice_id'];
                    $payment->pymo_serie = $pymo_serie;
                    $payment->document_type = __('invoice.e_receipt');
                    $payment->save();
                }
                
                if(empty($invoice_pdfdata)) {
                    return new JsonResponse(['status' => 'error', 'message' => 'Invoice issued, but failed to create pdf file!']);
                } 

                return $response;
            }
        } else {
            return new JsonResponse(['status' => 'error', 'message' => 'Failed to send Invoice!']);
        }
    }

    public function getInvoice(Request $request) {
        $business_id = session()->get('user.business_id');
        $invoice_id = $request->id;
        $userId = null;
        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }
        $pymoInfo = PymoAccount::where('user_id', $userId)->first();
        if (!$pymoInfo) {
            return new JsonResponse(['status' => 'error', 'message' => 'There is no pymo access information']);
        }
        $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
        if ($response['status'] != 'SUCCESS') {
            return new JsonResponse(['status' => 'error', 'message' => 'Pymo login failed!']);
        }

        $response = $this->pymoService->getInvoice($pymoInfo->rut, $invoice_id);

        $folderRelativePath = 'uploads/invoices/'.$business_id;
        $folderPath = public_path($folderRelativePath);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $filePath = public_path($folderRelativePath.'/'.$invoice_id.'.pdf');
        $result = file_put_contents($filePath, $response);

        if ($result !== false) {
            return response()->json(['status' => 'SUCCESS', 'url' => $folderRelativePath.'/'.$invoice_id.'.pdf' ], 200);
        } else {
            // Handle the case if file creation failed
            return response()->json(['message' => 'Failed to save PDF file'], 500);
        }
    }

    public function getGenerateInvoice() {
        return view("pymo.generateInvoice");
    }

    public function postGenerateInvoice() {
        $pymo_invoice = request()->input('pymo_invoice');
        $userId = null;

        if (auth()->user()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        try {
            $folderRelativePath = 'uploads/invoices/' . $user->business_id;
            $folderPath = public_path($folderRelativePath);
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            
            $filePath = public_path($folderRelativePath.'/'.$pymo_invoice.'.pdf');
            if(!file_exists($filePath)) {
                $pymoInfo = PymoAccount::where('user_id', $userId)->first();
                if (!$pymoInfo) {
                    return new JsonResponse(['success' => false, 'message' => 'There is no pymo access information']);
                }
                $response = $this->pymoService->login($pymoInfo->email, $pymoInfo->password);
                if ($response['status'] != 'SUCCESS') {
                    return new JsonResponse(['success' => false, 'message' => 'Pymo login failed!']);
                }
        
                $invoice_pdfdata = $this->pymoService->getInvoice($pymoInfo->rut, $pymo_invoice);
                if(!empty($invoice_pdfdata)) {
                    $result = file_put_contents($filePath, $invoice_pdfdata);
                    return response()->json([
                        'success' => true,
                        'message' => 'Invoice generated successfully',
                        'url' => '/'.$folderRelativePath.'/'.$pymo_invoice.'.pdf'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice already exists'
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
