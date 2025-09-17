@extends('layouts.app')
@section('title', __('lang_v1.all_payments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.payments')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        @include('transaction_payment.partials.payment_list_filters')
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_payments')])
        @include('contact.partials.contact_payments_tab')
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@stop

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    function get_contact_payments() {
        const location_id = $('#filter_location_id').val();
        const customer_id = $('#filter_customer_id').val();
        const created_by = $('#created_by').val();
        const searchTerm = $("input[name='searchTerm']").val();
        const trans_type = $("#trans_type").val();
        let url = `/payments?trans_type=${trans_type}&location_id=${location_id}&customer_id=${customer_id}&created_by=${created_by}`;
        if(searchTerm) url += `&searchTerm=${searchTerm}`;
        if($('#filter_date_range').val()) {
            var start = $('#filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            url += `&start_date=${start}`;
            url += `&end_date=${end}`;
        }

        window.location.href = url;
    }

    $(document).ready( function(){
        const currencies = @json($currencies);

        //Date range as a button
        $('#filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                get_contact_payments();
            }
        );
        $('#filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#filter_date_range').val('');
            get_contact_payments();
        });

        $(document).on('change', '#filter_location_id, #filter_customer_id, #filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #filter_source',  function() {
            get_contact_payments();
        });

        $('#only_subscriptions').on('ifChanged', function(event){
            get_contact_payments();
        });

        $("input[name='searchTerm']").on('keydown', function(event) {
            if(event.key == 'Enter') get_contact_payments();
        })

        @if($formattedStartDate && $formattedEndDate)
            $('#filter_date_range').val('{{$formattedStartDate}}' + ' ~ ' + '{{$formattedEndDate}}');
        @endif
    });
</script>
@endsection