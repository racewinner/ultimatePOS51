<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;

class PymoService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            // 'base_uri' => 'https://gateway.pymo.uy:443/v1/', 
            'base_uri' => 'https://gatewaytest.pymo.uy:443/v1/',
            'timeout' => 300,
        ]);
    }

    public function getUrl() {
        return 'https://gatewaytest.pymo.uy:443/v1/';
    }

    public function register($name, $email, $password)
    {
        $response = $this->client->post('register', [
            'json' => [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function login($email, $password)
    {
        try {
            $response = $this->client->post('login', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]);
            $cookieValue = $response->getHeader('Set-Cookie')[0];
            $result = json_decode($response->getBody()->getContents(), true);
            Session::put('pymo_token', $cookieValue);
            $result['cookie'] = $cookieValue;
            return $result;
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }
    }

    public function logout() {
        try {
            $cookie = Session::get('pymo_token');
            $response = $this->client->post('logout',[
                'headers' => [
                    'Cookie' => $cookie,
                ]
            ]);
            Session::forget('pymo_token');
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }
    }

    public function sendInvoice($RUT, $Room, $data)
    {
        try {
            $cookie = Session::get('pymo_token');
            $response = $this->client->post('companies/'.$RUT.'/sendCfes/'.$Room, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
                'json' => $data,
            ]);
    
            return json_decode($response->getBody()->getContents(), true);
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }
    }

    public function getInvoice($RUT, $id)
    {
        $cookie = Session::get('pymo_token');
        $response = $this->client->get('companies/'.$RUT.'/invoices?id='.$id, [
            'headers' => [
                'Cookie' => $cookie,
            ]
        ]);

        return $response->getBody()->getContents();;
    }

    public function getCompany($RUT) {
        $cookie = Session::get('pymo_token');
        try {
            $response = $this->client->get('companies/'.$RUT, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            if ($result['status'] == 'SUCCESS') {
                Session::put('pymo_room', $result['payload']['company']['branchOffices'][0]['number']);
                Session::put('pymo_rut', $result['payload']['company']['rut']);
            }
            return $result;
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }
    }

    public function getCfeDetail($RUT, $cfeType, $serie, $nro)
    {
        $t = $cfeType;
        if($cfeType === '911') $t = '111';
        if($cfeType === '901') $t = '101';
        $url = "companies/$RUT/sentCfes?cfeType=$t&nro=$nro&serie=$serie";

        $cookie = Session::get('pymo_token');

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);

            if($result['status'] == 'SUCCESS') {
                $cfes = $result['payload']['companySentCfes'];
                if(empty($cfes)) return ['status' => 'FAIL'];

                $data = [
                    "_id" => $cfes[0]['_id'],
                    "clientEmissionId" => $cfes[0]['clientEmissionId'],
                    "cfeType" => $cfes[0]["cfeType"],
                    "cfeDate" => $cfes[0]["cfeDate"],
                    "MntPagar" => $cfes[0]["MntPagar"],
                    "actualCfeStatus" => $cfes[0]["actualCfeStatus"],
                    "cfeHistory" => $cfes[0]["cfeHistory"],
                ];

                if($cfeType == '111') {                             // e-Invoice & business
                    $data['cfeTypeStr'] = "e-Invoice";
                    $cfe = $cfes[0]['cfe']['eFact'];
                    $data["document_type"] = ($cfes[0]["inCfeIdDoc"]["FmaPago"] == "1") ? __('invoice.e_invoice_cash') : __('invoice.e_invoice_credit');
                } else if($cfeType == '101') {                      // e-Ticket & person
                    $data['cfeTypeStr'] = "e-Ticket";
                    $cfe = $cfes[0]['cfe']['eTck'];
                    $data["document_type"] = ($cfes[0]["inCfeIdDoc"]["FmaPago"] == "1") ? __('invoice.e_invoice_cash') : __('invoice.e_invoice_credit');
                } else if($cfeType == '181') {                      // e-Remito & business
                    $data['cfeTypeStr'] = "eRemito";
                    $cfe = $cfes[0]['cfe']['eRem'];
                    $data["document_type"] =  '';
                } else if($cfe == '281') {                          // e-Remito & person

                } else if($cfeType == '112') {                      // e-Invoice-return & business
                    $data['cfeTypeStr'] = "NC eFactura";
                    $cfe = $cfes[0]['cfe']['eFact'];
                    $data["document_type"] =  '';
                } else if($cfeType == '102') {                      // e-Ticket-Return & person
                    $data['cfeTypeStr'] = "NC eTicket";
                    $cfe = $cfes[0]['cfe']['eTck'];
                    $data["document_type"] =  '';
                } else if($cfeType == '911') {                      // e-Recept & business
                    $data['cfeTypeStr'] = "eFactura - Cobranza";
                    $cfe = $cfes[0]['cfe']['eFact'];
                    $data["document_type"] =  '';
                } else if($cfeType == '901') {
                    $data['cfeTypeStr'] = "eTicket - Cobranza";     // e-Recept & person
                    $cfe = $cfes[0]['cfe']['eTck'];
                    $data["document_type"] =  '';
                }

                $data = [
                    ...$data,
                    "serie" => $cfe['Encabezado']['IdDoc']['Serie'],
                    "nro" => $cfe['Encabezado']['IdDoc']['Nro'],
                    "date" => $cfe['Encabezado']['IdDoc']['FchEmis'],
                    "transmitter" => [
                        "rut" => $cfe['Encabezado']['Emisor']['RUCEmisor'],
                        "name" => $cfe['Encabezado']['Emisor']['RznSoc'],
                        "telephone" => $cfe['Encabezado']['Emisor']['Telefono'],
                        "street" => $cfe['Encabezado']['Emisor']['DomFiscal'],
                        "city" => $cfe['Encabezado']['Emisor']['Ciudad'],
                        "department" => $cfe['Encabezado']['Emisor']['Departamento'],
                    ],
                    "receiver" => [
                        "country_code" => $cfe['Encabezado']['Receptor']['CodPaisRecep'],
                        "rut" => $cfe['Encabezado']['Receptor']['DocRecep'],
                        "name" => $cfe['Encabezado']['Receptor']['RznSocRecep'],
                        "street" => $cfe['Encabezado']['Receptor']['DirRecep'],
                        "city" => $cfe['Encabezado']['Receptor']['CiudadRecep'],
                    ],
                    "Totales" => [
                        "currency" => $cfe['Encabezado']['Totales']['TpoMoneda'],
                        "tpo_cambio" => $cfe['Encabezado']['Totales']['TpoCambio'],
                        "subtotal" => $cfe['Encabezado']['Totales']['MntNetoIVATasaBasica'],
                        "iva_tax_basic" => $cfe['Encabezado']['Totales']['IVATasaBasica'],
                        "iva_tax_min" => $cfe['Encabezado']['Totales']['IVATasaMin'],
                        "total_tax" => $cfe['Encabezado']['Totales']['MntIVATasaBasica'],
                        "total_amount" => $cfe['Encabezado']['Totales']['MntTotal'],
                        "total_pay" => $cfe['Encabezado']['Totales']['MntPagar'],
                    ],
                    "CAEData" => [
                        ...$cfe['CAEData']
                    ]
                ];

                $data["items"] = [];
                foreach($cfe['Detalle']['Item'] as $item) {
                    $data["items"][] = [
                        "name" => $item["NomItem"],
                        "amount" => $item["Cantidad"],
                        "unit" => $item["UniMed"],
                        "unit_price" => round(floatval($item["PrecioUnitario"] ?? '0'), 2),
                        "total_price" => round(floatval($item["MontoItem"] ?? '0'), 2)
                    ];
                }
                
                return ['status' => 'ok', 'data' => $data];
            } else {
                return ['status' => 'FAIL'];
            }

        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }        
    }

    public function purchaseCfes($RUT, $filter) 
    {
        $url = "companies/$RUT/inCfesAtDGI?";
        if(!empty($filter['limit']))  $url .= "&l=2";
        if(!empty($filter)) $url .= '&sk=' . $filter['skip'];
        if(!empty($filter['start_date'])) $url .= '&from=' . $filter['start_date'];
        if(!empty($filter['end_date'])) $url .= '&to=' . $filter['end_date'];

        $cookie = Session::get('pymo_token');
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }        
    }

    public function sentCfes($RUT, $filter)
    {
        $url = "companies/$RUT/sentCfes?f=actualCfeStatus&f=_id&f=cfeType&f=cfeDate";
        $url .= "&sk={$filter['skip']}";
        if(isset($filter["limit"]) && $filter["limit"] > 0) $url .= "&l=".$filter["limit"];
        if(isset($filter["cfeType"])) $url .= "&cfeType=".$filter['cfeType'];
        if(isset($filter["cfeStatus"])) $url .= "&actualCfeStatus=".$filter['cfeStatus'];
        if(isset($filter['start_date'])) $url .= "&cfeDate[gte]=".$filter['start_date'];
        if(isset($filter['end_date'])) $url .= "&cfeDate[lte]=".$filter['end_date'];

        $cookie = Session::get('pymo_token');
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }       
    }

    public function getCompanyCfesActiveNumbers($RUT)
    {
        $url = "companies/$RUT/cfesActiveNumbers?p=type";
        $cookie = Session::get('pymo_token');
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Cookie' => $cookie,
                ],
            ]);
            $jsonData = json_decode($response->getBody()->getContents(), true);
            if($jsonData['status'] != "SUCCESS") return [];

            return $jsonData['payload']['companyCfesActiveNumbers'];
        }catch(\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return ['status' => 'FAIL'];
        }
    }
}