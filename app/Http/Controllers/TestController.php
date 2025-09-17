<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \biller\bcu\Cotizaciones;
use App\CashRegisterTransaction;
use App\Transaction;
use App\PymoAccount;
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

use App\Services\PymoService;

class TestController extends Controller
{
    protected function getInvoiceData(&$invoice_data, &$qrcodeUri) {
        $invoice_data = [
            "_id" => "67183c97a710cae8ea63736b",
            "clientEmissionId" => "0634",
            "cfeType" => "111",
            "cfeTypeStr" => "e-Ticket",
            "cfeDate" => "2024-10-22T21:00:23-03:00",
            "actualCfeStatus" => "PROCESSED_REJECTED",
            "cfeHistory" => [],
            "serie" => "A",
            "nro" => "777",
            "doocument_type" => "Credit",
            "date" => "2024-10-23T00:00:23.000Z",
            "transmitter" => [
                "rut" => "216753930012",
                "name" => "MOTIONSOFT CONSULTING",
                "telephone" => "(+598) 4231 2345",
                "street" => "Miguelete 123",
                "city" => "Cerro Largo",
                "department" => "Mozambique",
            ],
            "receiver" => [
                "country_code" => "UY",
                "rut" => "216753930012",
                "name" => "FG INSTALACIONES",
                "street" => "Ituzaingó 1460",
                "city" => "Indefinido",
            ],
            "Totales" => [
                "currency" => "USD",
                "tpo_cambio" => 41.8,
                "subtotal" => 655.76,
                "iva_tax_basic" => 22,
                "iva_tax_min" => 10,
                "total_tax" => 144.27,
                "total_amount" => 800.02,
                "total_pay" => 800.02,
            ],
            "CAEData" => [
                "CAE_ID" => "90120000398",
                "DNro" => 1,
                "HNro" => 9999999,
                "FecVenc" => "2049-12-31"
            ],
            "items" => [
                [
                    "name" => "CAMARA WI-FI BULLET C3C ZKTECO",
                    "amount" => 3,
                    "unit" => "UN",
                    "unit_price" => 60.26,
                    "total_price" => 180.75
                ],
                [
                    "name" => "Nexxt Professional Cat5e UTP Cable 4P CM Outdoor 305m BL",
                    "amount" => 4,
                    "unit" => "UN",
                    "unit_price" => 118.75,
                    "total_price" => 475
                ]
            ]
        ];

        // Create QR code
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: 'https://www.efactura.dgi.gub.uy/consultaQR/cfe?216753930012,111,A,758,291.45,20241022,dpmLMfgpd%2FWbZvORW8oErIkY%2BDtbsUHnh2gnmZyu5HY%3D',
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
    }

    public function test1(Request $request)
    {
        $invoice_data = [];
        $qrcodeUri = "";
        $this->getInvoiceData($invoice_data, $qrcodeUri);

        // To generate pdf
        $pdf = PDF::loadView('test.test1', compact('invoice_data', 'qrcodeUri'));
        $pdf->setPaper([0, 0, 265, 1100]);

        $pdfContent = $pdf->output();
        file_put_contents("d://1.pdf", $pdfContent);

        return $pdf->download('test.pdf');
    }

    public function test2() 
    {
        $invoice_data = [];
        $qrcodeUri = "";
        $this->getInvoiceData($invoice_data, $qrcodeUri);

        return view("test.test1", compact('invoice_data', 'qrcodeUri'));
    }

    public function test3()
    {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = new QrCode(
            data: 'Life is too short to be generating QR codes',
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        
        $result = $writer->write($qrCode);

        $dataUri = $result->getDataUri();

        return $dataUri;
    }

    public function test4()
    {
        $logonUser = auth()->user();
        $folderRelativePath = 'uploads/invoices/' . $logonUser->business_id;
        $folderPath = public_path($folderRelativePath);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $count = 0;
        $pymoService = new PymoService();
        $done_businesses = [];

        // To get pymo account
        $pymoInfos = PymoAccount::with('user')->get();
        foreach($pymoInfos as $pymoInfo) {
            $count = 0;

            if(in_array($pymoInfo->user->business_id, $done_businesses)) continue;

            $response = $pymoService->login($pymoInfo->email, $pymoInfo->password);

            if ($response['status'] == 'SUCCESS') {
                $txs = Transaction::where('created_at', '>', '2025-04-17 00:00:00')
                    ->whereNotNull('pymo_invoice')
                    ->where('business_id', $pymoInfo->user->business_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
    
                foreach($txs as $tx) {
                    $filePath = public_path($folderRelativePath.'/'.$tx->pymo_invoice.'.pdf');
                    if(!file_exists($filePath)) {
                        $invoice_pdfdata = $pymoService->getInvoice($pymoInfo->rut, $tx->pymo_invoice);
                        if(!empty($invoice_pdfdata)) {
                            $result = file_put_contents($filePath, $invoice_pdfdata);
                            $count++;
                        }
                    }
                }
            }

            $done_businesses[] = $pymoInfo->user->business_id;
            echo $count . " invoice files were generated for " . $pymoInfo->user->username;
            $pymoService->logout();
        }
    }

    public function form_submit() 
    {
        $attributes = request()->input('attributes');
        dd($attributes);
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        return "cleared cache";
    }

    public function clearConfig()
    {
        \Artisan::call('config:clear');
        return "cleared config";
    }

    public function clearView()
    {
        \Artisan::call('view:clear');
        return "cleared view";
    }

    public function clearAll() {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        return "cleared all";
    }

    public function showPhpInfo() {
        ob_start(); // Start output buffering
        phpinfo(); // Output PHP info
        $phpinfo = ob_get_contents(); // Get the contents of the output buffer
        ob_end_clean(); // Clean the output buffer

        return view("test.php_ini", compact('phpinfo'));
    }
}