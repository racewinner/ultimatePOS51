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
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>CFE status</th>
                    <th>@lang('sale.customer_name')</th>
                    <th>@lang('lang_v1.contact_no')</th>
                    <th>@lang('sale.location')</th>
                    <th>@lang('invoice.document_type')</th>
                    <th>@lang('invoice.ref_invoie')</th>
                    <th>@lang('lang_v1.currency')</th>
                    <th>@lang('lang_v1.exchange_rate')</th>
                    <th>@lang('sale.total_amount')</th>
                    <th>@lang('sale.total_paid')</th>
                    <th>@lang('lang_v1.tax_amount')</th>
                    <th>@lang('lang_v1.amount_no_tax')</th>
                </tr>
                <tbody></tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="10"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_sale_total"></td>
                        <td class="footer_total_paid"></td>
                        <td class="footer_total_tax"></td>
                        <td class="footer_total_amount_no_tax"></td>
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
    $('#cfe_list_filter_date_range').daterangepicker(
        dateRangeSettings,
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
    $("select[name='cfe_table_length']").on('change', function(e) {
        console.log('----------')
    })

    cfe_table = $('#cfe_table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: false,
        searching: false,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/pymo/sentCfes",
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
            }
        },
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        columns: [
            // { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', orderable: false, "searchable": false},
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'cfeStatus', orderable: false, "searchable": false},
            { data: 'contact_name', name: 'contacts.supplier_business_name'},
            { data: 'mobile', name: 'contacts.mobile'},
            { data: 'business_location', name: 'bl.name'},
            { data: 'document_type', orderable: false, "searchable": false},
            { data: 'sell_return_pymo_serie', orderable: false, "searchable": false},
            { data: 'currency', name: 'currency_id', "searchable": false},
            { data: 'exchange_rate', name: 'exchange_rate', "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid', "searchable": false},
            { data: 'tax_amount', name: 'tax_amount', "searchable": false},
            { data: 'total_before_tax', name: 'total_before_tax', "searchable": false},
        ],
        "fnDrawCallback": function (oSettings) {
            
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var footer_sale_total = 0;
            var footer_sale_total_currency2 = 0;
            var footer_total_paid = 0;
            var footer_total_paid_currency2 = 0;
            var footer_total_tax = 0;
            var footer_total_tax_currency2 = 0;
            var footer_total_amount_no_tax = 0;
            var footer_total_amount_no_tax_currency2 = 0;

            for (var r in data){
                footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0;
                footer_sale_total_currency2 += $(data[r].final_total).data('orig-value-currency2') ? parseFloat($(data[r].final_total).data('orig-value-currency2')) : 0;

                footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                footer_total_paid_currency2 += $(data[r].total_paid).data('orig-value-currency2') ? parseFloat($(data[r].total_paid).data('orig-value-currency2')) : 0;

                footer_total_tax += $(data[r].tax_amount).data('orig-value') ? parseFloat($(data[r].tax_amount).data('orig-value')) : 0;
                footer_total_tax_currency2 += $(data[r].tax_amount).data('orig-value-currency2') ? parseFloat($(data[r].tax_amount).data('orig-value-currency2')) : 0;

                footer_total_amount_no_tax += $(data[r].total_before_tax).data('orig-value') ? parseFloat($(data[r].total_before_tax).data('orig-value')) : 0;
                footer_total_amount_no_tax_currency2 += $(data[r].total_before_tax).data('orig-value-currency2') ? parseFloat($(data[r].total_before_tax).data('orig-value-currency2')) : 0;
            }

            $('.footer_sale_total').html(
                __currency_trans_from(footer_sale_total, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(footer_sale_total_currency2, true, currencies[1]) : '')
            );
            $('.footer_total_paid').html(
                __currency_trans_from(footer_total_paid, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(footer_total_paid_currency2, true, currencies[1]) : '')
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