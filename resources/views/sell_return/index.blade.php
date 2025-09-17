@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.sell_return')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') . ':') !!}

            {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
            {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
        </div>
    </div>
    @can('access_sell_return')
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('created_by', __('report.user') . ':') !!}
                {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
            </div>
        </div>
    @endcan
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('authorized_by', __('lang_v1.authorizor') . ':') !!}
            {!! Form::select('authorized_by', $authorizors, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
        </div>
    </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.sell_return')])
    @include('sell_return.partials.sell_return_list')
    @endcomponent
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@include('sell_return.partials.invoice_layout_select_modal')

<!-- /.content -->
@stop
@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        var curr_tx_id = 0;

        $(document).ready(function () {
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    sell_return_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_return_table.ajax.reload();
            });

            sell_return_table = $('#sell_return_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'desc']],
                "ajax": {
                    "url": "/sell-return",
                    "data": function (d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }

                        if ($('#sell_list_filter_location_id').length) {
                            d.location_id = $('#sell_list_filter_location_id').val();
                        }
                        d.customer_id = $('#sell_list_filter_customer_id').val();

                        if ($('#created_by').length) {
                            d.created_by = $('#created_by').val();
                        }

                        if ($('#authorized_by').length) {
                            d.authorized_by = $('#authorized_by').val();
                        }
                    }
                },
                columnDefs: [{
                    "targets": [8, 9],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'invoice_no', name: 'invoice_no' },
                    { data: 'document_type', name: 'document_type' },
                    { data: 'parent_sale', name: 'T1.invoice_no' },
                    { data: 'parent_invoice', name: 'parent_invoice' },
                    { data: 'name', name: 'contacts.name' },
                    { data: 'business_location', name: 'bl.name' },
                    //{ data: 'payment_status', name: 'payment_status'},
                    { data: 'payment_status', name: 'payment_status', visible: false, orderable: false, searchable: false },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'payment_due', name: 'payment_due' },
                    { data: 'authorizor', orderable: false, searchable: false },
                    { data: 'action', name: 'action' }
                ],
                "fnDrawCallback": function (oSettings) {
                    var total_sell_currency1 = sum_table_col($('#sell_return_table'), 'final_total');
                    var total_sell_currency2 = sum_table_col($('#sell_return_table'), 'final_total', 'orig-value-currency2');
                    $('#footer_sell_return_total').html(
                        __currency_trans_from(total_sell_currency1, true, currencies[0]) +
                        (currencies.length > 1 ? ' / ' + __currency_trans_from(total_sell_currency2, true, currencies[1]) : '')
                    );

                    $('#footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));

                    var total_due_currency1 = sum_table_col($('#sell_return_table'), 'payment_due');
                    var total_due_currency2 = sum_table_col($('#sell_return_table'), 'payment_due', 'orig-value-currency2');
                    $('#footer_total_due_sr').html(
                        __currency_trans_from(total_due_currency1, true, currencies[0]) +
                        (currencies.length > 1 ? ' / ' + __currency_trans_from(total_due_currency2, true, currencies[1]) : '')
                    );

                    __currency_convert_recursively($('#sell_return_table'));
                },
                createdRow: function (row, data, dataIndex) {
                    $(row).find('td:eq(3)').attr('class', 'clickable_td');
                }
            });
            $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by, #authorized_by', function () {
                sell_return_table.ajax.reload();
            });

            $(document).on('click', '.return-invoice-print', function () {
                curr_tx_id = $(this).data('id');
                $("#modal_select_invoice_layout").modal("show");
            })

            $(document).on('click', '#modal_select_invoice_layout button#btn_confirm', function () {
                const pdf_format = $("select[name='pdf_format']").val()
                const url = `/sell-return/print/${curr_tx_id}/${pdf_format}`;
                $('.print-invoice').attr('data-href', url);
                $('.print-invoice').trigger('click');
                $("#modal_select_invoice_layout").modal("hide");
            })
        })

        $(document).on('click', 'a.delete_sell_return', function (e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                sell_return_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    </script>

@endsection