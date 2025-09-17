@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.business_settings')</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\BusinessController::class, 'postBusinessSettings']), 
                'method' => 'post', 
                'id' => 'bussiness_edit_form',
                'onsubmit' => 'return onEditBusinessFormSubmit()',
                'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
       <!--  <pos-tab-container> -->
        <div class="col-xs-12 pos-tab-container">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                <div class="list-group">
                    <a href="#" class="list-group-item text-center active">@lang('business.business')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.exchg_rate_history')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</a>
                    <a href="#" class="list-group-item text-center">@lang('business.product')</a>
                    <a href="#" class="list-group-item text-center">@lang('contact.contact')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.sale')</a>
                    <a href="#" class="list-group-item text-center">@lang('sale.pos_sale')</a>
                    <a href="#" class="list-group-item text-center">@lang('purchase.purchases')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.payment')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.dashboard')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.system')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.prefixes')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.email_settings')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.sms_settings')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.reward_point_settings')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.modules')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.custom_labels')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.bell_notifications')</a>
                    <a href="#" class="list-group-item text-center">@lang('notifications.notification_users')</a>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
                @include('business.partials.settings_business')
                <!-- tab 1 end -->
                @include('business.partials.nation_exchg_rate_history')
                <!-- tab 2 start -->
                @include('business.partials.settings_tax')
                <!-- tab 2 end -->
                <!-- tab 3 start -->
                @include('business.partials.settings_product')

                @include('business.partials.settings_contact')
                <!-- tab 3 end -->
                <!-- tab 4 start -->
                @include('business.partials.settings_sales')
                @include('business.partials.settings_pos')
                <!-- tab 4 end -->
                <!-- tab 5 start -->
                @include('business.partials.settings_purchase')

                @include('business.partials.settings_payment')
                <!-- tab 5 end -->
                <!-- tab 6 start -->
                @include('business.partials.settings_dashboard')
                <!-- tab 6 end -->
                <!-- tab 7 start -->
                @include('business.partials.settings_system')
                <!-- tab 7 end -->
                <!-- tab 8 start -->
                @include('business.partials.settings_prefixes')
                <!-- tab 8 end -->
                <!-- tab 9 start -->
                @include('business.partials.settings_email')
                <!-- tab 9 end -->
                <!-- tab 10 start -->
                @include('business.partials.settings_sms')
                <!-- tab 10 end -->
                <!-- tab 11 start -->
                @include('business.partials.settings_reward_point')
                <!-- tab 11 end -->
                <!-- tab 12 start -->
                @include('business.partials.settings_modules')
                <!-- tab 12 end -->
                @include('business.partials.settings_custom_labels')
                <!-- tab 13 start -->
                @include('business.bell_notifications.settings_bell_notifications')
                <!-- tab 13 end -->
                <!-- tab 14 start -->
                @include('business.bell_notifications.receivers_notifications')
                <!-- tab 14 end -->
            </div>
        </div>
        <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="btn btn-danger btn-big" id="btn_update" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->

@include('business.bell_notifications.add_receiver_modal')

@stop


@section('javascript')
@yield('notification_javascript')
@yield('receiver_javascript')
<script type="text/javascript">
    __page_leave_confirmation('#bussiness_edit_form');
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });

    function onEditBusinessFormSubmit(e) {
        const first_currency_id = $("select#currency_id").val();
        const second_currency_id = $("select#second_currency_id").val();
        const nation_currency_id = $("select#nation_currency_id").val();
        const nation_exchg_rate = $("input[name='nation_exchg_rate']").val();

        if( first_currency_id == second_currency_id) {
            setTimeout(() => {
                $("button#btn_update").attr("disabled", false);
            }, 1000);
            toastr.error("Please select the correct second currency!");
            return false;
        }

        if( (first_currency_id != nation_currency_id) && (second_currency_id != nation_currency_id)) {
            setTimeout(() => {
                $("button#btn_update").attr("disabled", false);
            }, 1000);
            toastr.error("Please select the correct nation currency!");
            return false;
        }

        if(second_currency_id && nation_exchg_rate <= 0) {
            setTimeout(() => {
                $("button#btn_update").attr("disabled", false);
            }, 1000);
            toastr.error("Please input the correct exchange rate!");
            return false;
        }

        return;
    }

    $(document).ready(function(){
        $("#second_currency_id").change(function(e) {
            // $("input[name='nation_exchg_rate']").attr('readonly', !(e.currentTarget.value > 0));
        });


        var ranges = {};
        ranges[LANG.last_7_days] = [moment().subtract(6, 'days'), moment()];
        ranges[LANG.last_30_days] = [moment().subtract(29, 'days'), moment()];
        ranges[LANG.this_month] = [moment().startOf('month'), moment().endOf('month')];
        ranges[LANG.last_month] = [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month'),
        ];
        var dateRangeSettings = {
            ranges: ranges,
            startDate: moment().subtract(9, 'days'),
            endDate: moment(),
            locale: {
                cancelLabel: LANG.clear,
                applyLabel: LANG.apply,
                customRangeLabel: LANG.custom_range,
                format: moment_date_format,
                toLabel: '~',
            },
        };
        $('#nerh_date_range').daterangepicker(
            dateRangeSettings, 
            function(start, end) {
                $('#nerh_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            }
        );
        $('#nerh_date_range').change( function(){
            var start = $('input#nerh_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('input#nerh_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            const url = `/business/nerh/${start}/${end}`;
            $("#nerh_iframe").attr('src', url);

            $.ajax({
                url,
                success: function(response) {
                    if(response.success == 1) {
                        $tbody = $("#nerh_table tbody");
                        $tbody.html('');
                        for(let i=0; i<response.data.length; i++ ){
                            const d = response.data[i];
                            const tr = document.createElement('tr');

                            let td = document.createElement('td');
                            td.innerHTML = d.date;
                            tr.appendChild(td);

                            td = document.createElement('td');
                            td.innerHTML = d.nation_exchg_rate;
                            tr.appendChild(td);

                            $tbody.append(tr);
                        }
                    } else {
                        toastr.error("Something went wrong.");
                    }
                }
            });
        });
        $('#nerh_date_range').trigger('change');

        $('#test_email_btn').click( function() {
            var data = {
                mail_driver: $('#mail_driver').val(),
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_username: $('#mail_username').val(),
                mail_password: $('#mail_password').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_from_address: $('#mail_from_address').val(),
                mail_from_name: $('#mail_from_name').val(),
            };
            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testEmailConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        $('#test_sms_btn').click( function() {
            var test_number = $('#test_number').val();
            if (test_number.trim() == '') {
                toastr.error('{{__("lang_v1.test_number_is_required")}}');
                $('#test_number').focus();

                return false;
            }

            var data = {
                url: $('#sms_settings_url').val(),
                send_to_param_name: $('#send_to_param_name').val(),
                msg_param_name: $('#msg_param_name').val(),
                request_method: $('#request_method').val(),
                param_1: $('#sms_settings_param_key1').val(),
                param_2: $('#sms_settings_param_key2').val(),
                param_3: $('#sms_settings_param_key3').val(),
                param_4: $('#sms_settings_param_key4').val(),
                param_5: $('#sms_settings_param_key5').val(),
                param_6: $('#sms_settings_param_key6').val(),
                param_7: $('#sms_settings_param_key7').val(),
                param_8: $('#sms_settings_param_key8').val(),
                param_9: $('#sms_settings_param_key9').val(),
                param_10: $('#sms_settings_param_key10').val(),

                param_val_1: $('#sms_settings_param_val1').val(),
                param_val_2: $('#sms_settings_param_val2').val(),
                param_val_3: $('#sms_settings_param_val3').val(),
                param_val_4: $('#sms_settings_param_val4').val(),
                param_val_5: $('#sms_settings_param_val5').val(),
                param_val_6: $('#sms_settings_param_val6').val(),
                param_val_7: $('#sms_settings_param_val7').val(),
                param_val_8: $('#sms_settings_param_val8').val(),
                param_val_9: $('#sms_settings_param_val9').val(),
                param_val_10: $('#sms_settings_param_val10').val(),
                test_number: test_number
            };

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testSmsConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        $('select.custom_labels_products').change(function(){
            value = $(this).val();
            textarea = $(this).parents('div.custom_label_product_div').find('div.custom_label_product_dropdown');
            if(value == 'dropdown'){
                textarea.removeClass('hide');
            } else{
                textarea.addClass('hide');
            }
        })

        $("button#btn_get_from_bcu").click(function(){
           $.ajax({
            method: 'get',
            url: "{{ action([\App\Http\Controllers\BcuController::class, 'getCurrentRate']) }}",
            dataType: "json",
            success: function(response) {
                $("input[name='nation_exchg_rate']").val(response.exchg_rate)
            }
           }) 
        });

    });
</script>
@endsection