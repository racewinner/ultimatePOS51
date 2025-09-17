@extends('layouts.app')
@section('title', __( 'lang_v1.all_remitos'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'lang_v1.remitos')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        @include('remito.partials.remito_list_filters')
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_remitos')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\RemitoController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endslot
        <table class="table table-bordered table-striped ajax_view w-100" id="remito_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>@lang('sale.customer_name')</th>
                    <th>@lang('lang_v1.contact_no')</th>
                    <th>@lang('sale.location')</th>
                    <th>@lang('invoice.document_type')</th>
                    <th>@lang('lang_v1.added_by')</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    @endcomponent
</section>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    $('#remito_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#remito_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            remito_table.ajax.reload();
        }
    );
    $('#remito_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#remito_list_filter_date_range').val('');
        remito_table.ajax.reload();
    });

    remito_table = $('#remito_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/remitos",
            "data": function ( d ) {
                if($('#remito_list_filter_date_range').val()) {
                    var start = $('#remito_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#remito_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                d.is_direct_sale = 1;

                d.location_id = $('#remito_list_filter_location_id').val();
                d.customer_id = $('#remito_list_filter_customer_id').val();
                d.created_by = $('#created_by').val();

                d = __datatable_ajax_callback(d);
            }
        },
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', name: 'transaction_date', orderable: false},
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'contact_name', name: 'contacts.supplier_business_name'},
            { data: 'mobile', name: 'contacts.mobile'},
            { data: 'location_name', name: 'bl.name'},
            { data: 'document_type', orderable: false, "searchable": false},
            { data: 'added_by', name: 'u.first_name'},
        ],
        "fnDrawCallback": function (oSettings) {
            // __currency_convert_recursively($('#remito_table'));
        },
        createdRow: function( row, data, dataIndex ) {
        }
    });

    $(document).on('change', '#remito_list_filter_location_id, #remito_list_filter_customer_id, #created_by',  function() {
        remito_table.ajax.reload();
    });

    //Delete remito
    $(document).on('click', '.delete-remito', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var is_suspended = $(this).hasClass('is_suspended');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            remito_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
});
</script>
@endsection