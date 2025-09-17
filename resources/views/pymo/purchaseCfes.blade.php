@extends('layouts.app')
@section('title', __( 'lang_v1.invoices'))

@section('content')
<style type="text/css">
    #cfe_table_paginate {
        display: none;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.invoices')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        @include('pymo.partials.cfe_list_filters')
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.invoices')])
        <table class="table table-bordered table-striped ajax_view w-100" id="cfe_table">
            <thead>
                <tr>
                    <!-- <th>@lang('messages.action')</th> -->
                    <th>@lang('messages.status')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('messages.serie')</th>
                    <th>@lang('messages.number')</th>
                    <th>@lang('invoice.document_type')</th>
                    <th>@lang('invoice.issuer_rut')</th>
                    <th>@lang('purchase.supplier')</th>
                    <th>@lang('business.currency')</th>
                    <th>@lang('lang_v1.amount_no_tax')</th>
                    <th>@lang('lang_v1.tax_amount')</th>
                    <th>@lang('sale.total_amount')</th>
                </tr>
                <tbody></tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="8"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_total_amount_no_tax"></td>
                        <td class="footer_total_tax"></td>
                        <td class="footer_total_amount"></td>
                    </tr>
                </tfoot>
            </thead>
        </table>
        <div class="d-flex justify-content-end" style="margin-top: 20px;">
            <button type="button" id="prev_btn" class="btn btn-primary">Prev</button>
            <input type="number" name="cur_page" id="cur_page" class="form-control text-center" value='1'
                style="width: 50px; margin-left: 10px; margin-right: 10px;" />
            <button type="button" id="next_btn" class="btn btn-primary">Next</button>
        </div>
    @endcomponent
</section>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    const currencies = @json($currencies);

    //Date range as a button
    const endDate = new Date();
    const startDate = new Date(endDate);
    startDate.setDate(endDate.getDate() - 7);
    $('#cfe_list_filter_date_range').daterangepicker(
        {
            ...dateRangeSettings,
            startDate,
            endDate
        },
        function (start, end) {
            $('#cfe_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            cfe_table.ajax.reload();
        }
    );
    $('#cfe_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#cfe_list_filter_date_range').val('');
        cfe_table.ajax.reload();
    });
    $("#cfeType").on('change', function(e) {
        cfe_table.ajax.reload();
    })
    $("#cfeStatus").on('change', function(e) {
        cfe_table.ajax.reload();
    })
    $("button#prev_btn").on('click', function(e) {
        const cur_page = parseInt($("input#cur_page").val());
        if(cur_page > 1) {
            $("input#cur_page").val(cur_page - 1);
            cfe_table.ajax.reload();
        }
    })
    $("button#next_btn").on('click', function(e) {
        const cur_page = parseInt($("input#cur_page").val());
        $("input#cur_page").val(cur_page + 1);
        cfe_table.ajax.reload();
    })

    cfe_table = $('#cfe_table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: false,
        searching: false,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/pymo/purchaseCfes",
            "data": function ( d ) {
                if($('#cfe_list_filter_date_range').val()) {
                    var start = $('#cfe_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#cfe_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                d.cfeType = $("#cfeType").val();
                d.cfeStatus = $("#cfeStatus").val();
                d.length = $("select[name='cfe_table_length']").val();
                d.start = (parseInt($("#cur_page").val()) - 1) * d.length;

                d = __datatable_ajax_callback(d);
            },
            error: function(xhr, error, thrown) {
                toastr.error(xhr.responseJSON.message);
            }
        },
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        columns: [
            { data: 'Estado', orderable: false, "searchable": false},
            { data: 'FechaHora', orderable: false, "searchable": false},
            { data: 'Serie', orderable: false, "searchable": false},
            { data: 'Nro', orderable: false, "searchable": false},
            { data: 'TipoCFE', orderable: false, "searchable": false},
            { data: 'RucEmisor', orderable: false, "searchable": false},
            { data: 'provider', orderable: false, "searchable": false},
            { data: 'Moneda', orderable: false, "searchable": false},
            { data: 'TotalNeto', orderable: false, "searchable": false},
            { data: 'TotalIVA', orderable: false, "searchable": false},
            { data: 'MontoTotal', orderable: false, "searchable": false},
        ],
        "fnDrawCallback": function (oSettings) {
            
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var footer_total_tax = 0;
            var footer_total_tax_currency2 = 0;

            var footer_total_amount = 0;
            var footer_total_amount_currency2 = 0;

            var footer_total_amount_no_tax = 0;
            var footer_total_amount_no_tax_currency2 = 0;

            for (var r in data){
                footer_total_amount += $(data[r].MontoTotal).data('orig-value') ? parseFloat($(data[r].MontoTotal).data('orig-value')) : 0;
                footer_total_amount_currency2 += $(data[r].MontoTotal).data('orig-value-currency2') ? parseFloat($(data[r].MontoTotal).data('orig-value-currency2')) : 0;

                footer_total_tax += $(data[r].TotalIVA).data('orig-value') ? parseFloat($(data[r].TotalIVA).data('orig-value')) : 0;
                footer_total_tax_currency2 += $(data[r].TotalIVA).data('orig-value-currency2') ? parseFloat($(data[r].TotalIVA).data('orig-value-currency2')) : 0;

                footer_total_amount_no_tax += $(data[r].TotalNeto).data('orig-value') ? parseFloat($(data[r].TotalNeto).data('orig-value')) : 0;
                footer_total_amount_no_tax_currency2 += $(data[r].TotalNeto).data('orig-value-currency2') ? parseFloat($(data[r].TotalNeto).data('orig-value-currency2')) : 0;
            }

            $('.footer_total_amount').html(
                __currency_trans_from(footer_total_amount, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(footer_total_amount_currency2, true, currencies[1]) : '')                
            );

            $('.footer_total_tax').html(
                __currency_trans_from(footer_total_tax, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(footer_total_tax_currency2, true, currencies[1]) : '')
            );

            $('.footer_total_amount_no_tax').html(
                __currency_trans_from(footer_total_amount_no_tax, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(footer_total_amount_no_tax_currency2, true, currencies[1]) : '')
            );
        },
        createdRow: function( row, data, dataIndex ) {
        }
    });
});
</script>
@endsection