<!DOCTYPE html>
<html>
<head>
    <title>Title</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 350px;
            color: #000;
            margin:0px;
        }
        @page {
            margin: 0px;
        }
    </style>
</head>
<body>

<section style="padding: 10px;">
    <div style="text-align:center;">
        <img src="{{ $logoFilePath }}" style="height:100px;" />
    </div>

    <div style="display:flex; flex-direction:column;align-items:center; text-align:center; margin-top:10px; font-size: 110%;">
        <div style='text-transform: uppercase'>@lang('invoice.seller_informatoin')</div>
        <div>{{ $invoice_data['transmitter']['name'] }}</div>
        <div>{{ $invoice_data['transmitter']['telephone'] }}</div>
        <div>{{ $invoice_data['transmitter']['rut'] }}</div>
    </div>

    <table style="margin-top: 20px; font-size: 120%; width:100%;">
        <tr>
            <td style="text-align:left;">{{ $invoice_data['cfeTypeStr'] }}</td>
            <td style="text-align:right;">{{ $invoice_data['document_type'] }}</td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 5px;">
        <tr>
            <td style="text-align:left;">{{ $invoice_data['serie'] . $invoice_data['nro'] }}</td>
            <td style="text-align:center;">{{ formatDate($invoice_data['date']) }}</td>
            <td style="text-align:right;">{{ $invoice_data['Totales']['currency'] }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px;font-weight: bold;">
        <div style="border: 1px solid #000; padding: 5px; text-align:center; text-transform:uppercase">@lang('invoice.client_data')</div>
        <div style="padding: 10px; border:1px solid #000; border-top-width: 0;">
            <div style="text-align:center">{{ $invoice_data['receiver']['name'] }}</div>
            <div style="text-align:center">{{ $invoice_data['receiver']['rut'] }}</div>
            <div style="text-align:center">{{ $invoice_data['receiver']['street'] }}</div>
            <div style="text-align:center">{{ $invoice_data['receiver']['city'] }}</div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <table style="font-size: 90%; width: 100%;"> 
            <thead>
                <tr>
                    <th style="text-align:left; border:none; text-transform:uppercase">@lang('invoice.product')</th>
                    <th style="border:none; text-transform:uppercase">@lang('invoice.quant')</th>
                    @if($cfeType != '181' && $cfeType != '281')
                    <th style="border:none; text-transform:uppercase">@lang('invoice.price')</th>
                    <th style="border:none;">VAT</th>
                    <th style="border:none; text-transform:uppercase">@lang('invoice.total')</th>
                    @endif
                </tr>
            </thead>
            <tbody style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            @foreach($invoice_data['items'] as $item)
                <tr style="border: none;">
                    <td style="border: none; text-align:left; padding-top:5px; padding-bottom: 5px;">{{$item['name']}}</td>
                    <td style="border: none; text-align:center;">{{ $item['amount'] }}</td>
                    @if($cfeType != '181' && $cfeType != '281')
                    <td style="border: none; text-align:center;">{{ $item['unit_price'] }}</td>
                    <td style="border: none; text-align:center;">{{ $invoice_data['Totales']['iva_tax_basic'] }}</td>
                    <td style="border: none; text-align:right;">{{ $item['total_price'] }}</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($cfeType == '111' || $cfeType == '101' || $cfeType == '112' || $cfeType == '102')
    <div style="text-align:right; margin-top: 20px;">
        <div style="padding: 5px 0px;">
            <label style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.sub_total'):</label>
            <span style="margin-left: 10px;">{{ $invoice_data['Totales']['currency'] }} {{ $invoice_data['Totales']['subtotal'] }}</span>
        </div>
        <div style="padding: 5px 0px;">
            <label  style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.total_tax'):</label>
            <span style="margin-left: 10px;">{{ $invoice_data['Totales']['currency']  }} {{ $invoice_data['Totales']['total_tax'] }}</span>
        </div>
        <div style="padding: 5px 0px; font-weight: bold;">
            <label style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.total'):</label>
            <span style="margin-left: 10px;">{{ $invoice_data['Totales']['currency']  }} {{ $invoice_data['Totales']['total_amount'] }}</span>
        </div>
    </div>
    @endif

    @if($cfeType == '911' || $cfeType == '901')
    <div style="text-align:right; margin-top: 20px;">
        <div style="padding: 5px 0px; font-weight: bold;">
            <label style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.total'):</label>
            <span style="margin-left: 10px;">{{ $invoice_data['Totales']['currency']  }} {{ $invoice_data['Totales']['total_pay'] }}</span>
        </div>
    </div>
    @endif



    <h3 style="margin-top:20px; margin-bottom:5px; text-align:center; text-transform:uppercase">@lang('invoice.electronic_invoice_data')</h3>
    <div style="border: 2px solid #000; font-size: 90%;">
        <div style="text-align:center; padding: 10px;">
            <img src={{$qrcodeUri}} style="width: 200px; height: 200px;" />
        </div>

        <div style="text-align:center; padding: 15px;">
            <div style="text-align:center;">Res. No.. 001/2018.</div>
            <div>You can verify the voucher at</div>
            <div style="text-align:center; overflow-wrap: anywhere;">https://www.efactura.dgi.gub.uy/principal/verificacioncfe</div>
        </div>

        <div style="text-align:center; padding: 15px;">
            <div>@lang('invoice.serie'): {{ $invoice_data['serie'] }} | @lang('invoice.number'): {{ $invoice_data['nro'] }}</div>
            <div>ER No.: {{ $invoice_data['CAEData']['CAE_ID'] }}</div>
            <div>@lang('invoice.range'): @lang('invoice.serie') {{ $invoice_data['serie'] }} @lang('invoice.from') No. {{ $invoice_data['CAEData']['DNro'] }} @lang('invoice.to') {{ $invoice_data['CAEData']['HNro'] }}</div>
            <div>@lang('invoice.security_code'): +WHzuW</div>
            <div>@lang('invoice.expiration_date'): {{ $invoice_data['CAEData']['FecVenc'] }}</div>
        </div>

        <div style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 10px; text-align: center; text-transform:uppercase">@lang('invoice.addendum')</div>
        <div style="padding: 8px;">
            {!! text2html($invoice_data['adenda']) !!}
        </div>
    </div>
</section>

</body>
</html>
