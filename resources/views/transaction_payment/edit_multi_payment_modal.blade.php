<div class="modal-dialog modal-xl" role="document">
    <form id="payment_form"
        data-action="{{action([\App\Http\Controllers\TransactionPaymentController::class, 'updateMultiPayment'])}}">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.multi_pay')</h4>
            </div>
            <div class="modal-body">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.multi_pay')])
                    <div class='d-flex align-items-center' style='position: absolute; top:30px; right: 50px;'>
                        <div class="d-flex align-items-center me-4">
                            <label class="me-2">Ref No : </label>
                            <input 
                                type='text' 
                                class="form-control w-150px" 
                                name='payment_ref_no' 
                                value="{{$payment_line->payment_ref_no}}" 
                            />
                        </div>
                        <div class="d-flex align-items-center">
                            <label class='me-2'>@lang('lang_v1.total_quantity') : </label>
                            <input type='number' class='form-control w-150px' readonly name='multi-pay-total-amount' />
                        </div>
                    </div>
                    <div>
                        <table class="table table-bordered table-striped ajax_view" id="multi-pay-table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.action')</th>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    <th>@lang('lang_v1.pymo_invoice_no')</th>
                                    <th>@lang('sale.customer_name')</th>
                                    <th>@lang('sale.location')</th>
                                    <th>@lang('invoice.document_type')</th>
                                    <th>@lang('lang_v1.currency')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('lang_v1.pay_amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($multi_pays as $paySell)
                                <tr>
                                    <td>
                                        <div class='btn-group d-flex justify-content-center'>
                                            <button type="button" class='btn btn-danger btn-xs delete-one-payment'
                                                data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'destroy'], [$paySell->payment_id]) }}">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                @lang("messages.delete")
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{@format_datetime($paySell->paid_on)}}</td>
                                    <td>{{$paySell->invoice_no}}</td>
                                    <td>{{$paySell->pymo_serie}}</td>
                                    <td>{{$paySell->supplier_business_name ?? $paySell->name}}</td>
                                    <td>{{$paySell->location}}</td>
                                    <td>
                                        @if(!empty($paySell->pymo_invoice))
                                            <a href='/uploads/invoices/{{$paySell->business_id}}/{{$paySell->pymo_invoice}}.pdf'
                                                class='ms-2' target='_blank'>
                                                {{$paySell->document_type}}
                                            </a>
                                        @else
                                            <a href='/invoice/transaction/{{$paySell->id}}' class='ms-2'
                                                target='_blank'>internal</a>
                                        @endif
                                    </td>
                                    <td>{{$paySell->currency->symbol}}</td>
                                    <td>{{\App\Utils\Util::format_currency($paySell->final_total, $paySell->currency, false)}}
                                    </td>
                                    <td>
                                        <div class='d-flex justify-content-center'>
                                            <input 
                                                type='number' 
                                                class='form-control pay-amount' 
                                                name='pay_amount'
                                                data-sell-id='{{$paySell->id}}' 
                                                data-payment-id='{{$paySell->payment_id}}'
                                                value='{{round($paySell->pay_amount, 2)}}'
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>

                    <div class="d-flex mt-4 payment_row align-items-start">
                        <div class="me-4">
                            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
                            <div class="input-group ms-2">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('paid_on', @format_datetime($payment_line?->paid_on ?? ''), 
                                    ['class' => 'form-control paid_on', 'readonly', 'required']) !!}
                            </div>
                        </div>
                        <div class="me-4 w-200px">
                            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
                            <div class="input-group ms-2">
                                <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i></span>
                                {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']) !!}
                            </div>
                        </div>
                        @include('transaction_payment.payment_type_details')
                    </div>
                    <div class="d-flex mt-4">
                        <div class="me-4">
                            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                            {!! Form::file('document', ['class' => 'ms-2', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]) !!}
                            <div>@includeIf('components.document_help_text')</div>
                        </div>
                        <div>
                            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
                            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                @endcomponent
            </div>
            <div class="modal-footer">
                <div class="d-flex">
                    @if($trans_type == 'sell')
                    <div class="invoice_container">
                        @if($payment_line->pymo_invoice)
                            <i class='fa fa-download'></i>
                            <a class='m-2' href='/uploads/invoices/{{$payment_line->business_id}}/{{$payment_line->pymo_invoice}}.pdf' target='_blank'>@lang('invoice.download_document')</a>
                        @else
                            <button type="button" class="btn btn-default" id='generate_invoice_btn'>@lang('invoice.generate_invoice')</button>
                        @endif
                    </div>
                    @endif
                    <div class="flex-fluid d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id='save_multi_pay'>@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        function calc_total_payAmount() {
            let total_amount = 0;
            const payInputs = document.querySelectorAll("table#multi-pay-table tbody input.pay-amount");
            payInputs.forEach(function (payInput) {
                total_amount += parseFloat(payInput.value);
            })
            $("input[name='multi-pay-total-amount']").val(floatV(total_amount));
        }

        $(document).off('change', 'input.pay-amount');
        $(document).on('change', 'input.pay-amount', function (e) {
            setTimeout(() => {
                calc_total_payAmount();
            }, 200);
        })

        $("form#payment_form")
            .submit(function (e) {
                e.preventDefault();
            })
            .validate({
                submitHandler: function (form) {
                    // prepare post data
                    const data = new FormData();
                    const paymentForm = $("form#payment_form");
                    paymentForm.serializeArray().forEach(item => {
                        // if item.name is 'pay_amount', then we would deal with them below.
                        if (item.name != 'pay_amount') {
                            data.append(item.name, item.value)
                        }
                    });

                    // To deal with pay_amount inputs
                    const payInputs = document.querySelectorAll("table#multi-pay-table tbody input.pay-amount");
                    const pays = [];
                    payInputs.forEach(payInput => {
                        pays.push({
                            payment_id: $(payInput).data("payment-id"),
                            amount: payInput.value
                        });
                    });
                    data.append('pays', JSON.stringify(pays));

                    // document file
                    const documentFile = $("#document");
                    if (documentFile[0].files.length > 0) {
                        data.set("document", documentFile[0].files[0]);
                    }

                    // To disable save button
                    $('button#save_multi_pay').prop("disabled", true);

                    // To send save request
                    const url = paymentForm.data('action');
                    $.ajax({
                        method: "post",
                        url: url,
                        dataType: "json",
                        data,
                        processData: false,
                        contentType: false,
                        success: function (result) {
                            $('button#save_multi_pay').prop("disabled", false);
                            if (result.success == true) {
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            })

        $(document).on('click', '.delete-one-payment', function (e) {
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_payment,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    $.ajax({
                        url: $(this).data('href'),
                        method: 'delete',
                        dataType: 'json',
                        success: function (result) {
                            const tr = e.target.closest('tr');
                            tr.remove();
                            calc_total_payAmount();
                        }
                    })
                }
            })
        })

        $(document).on('click', 'button#generate_invoice_btn', function (e) {
            const payInputs = document.querySelectorAll("table#multi-pay-table tbody input.pay-amount");
            const pays = [];
            payInputs.forEach(payInput => {
                pays.push({
                    payment_id: $(payInput).data("payment-id"),
                    amount: payInput.value
                });
            });
            const data = {
                is_multi_pay: 1,
                pays
            }

            $('#generate_invoice_btn').attr('disabled', true);

            $.ajax({
                url: '/pymo/sendReceiptInvoice',
                method: 'post',
                data,
                success: function (response) {
                    if (response.status && response.status === 'SUCCESS') {
                        toastr.success("Cfes recibidos correctamente.");
                        let html = "<i class='fa fa-download'></i>";
                        html += `<a class='m-2' href='/uploads/invoices/{{$payment_line->business_id}}/${response.invoice_id}.pdf' target='_blank'>@lang('invoice.download_document')</a>`;
                        $(".invoice_container").html(html);
                    }
                    if (response.status === 'error') {
                        if (response.data && response.data.message.code && response.data.message.code === 'DUPLICATED_KEY') {
                            toastr.warning("Please update Invoice no");
                        } else if (response.message) {
                            toastr.warning(response.message);
                        } else if (response.data && response.data.message) {
                            toastr.warning(response.data.message.value);
                        }
                        $('#generate_invoice_btn').attr('disabled', false);
                    }
                }
            })
        });

        calc_total_payAmount();
    })
</script>