<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.multi_pay')</h4>
        </div>
        <div class="modal-body">
            <input type='hidden' name='contact_id' value={{request()->contact_id}} />
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @if(auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only') || auth()->user()->can('view_commission_agent_sell'))
                <table class="table table-bordered table-striped ajax_view" id="sell_due_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('invoice.document_type')</th>
                            <th>@lang('lang_v1.currency')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.sell_return_due')</th>
                            <th>@lang('lang_v1.tax_amount')</th>
                            <th>@lang('lang_v1.exchange_rate')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            @endif
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.multi_pay')])
            <div class='d-flex align-items-center' style='position: absolute; top:30px; right: 50px;'>
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
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('invoice.document_type')</th>
                            <th>@lang('lang_v1.currency')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.pay_amount')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div>

                    <form id="payment_form"
                        data-action="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'storeMultiPayment']) }}">
                        <div class="d-flex mt-4 payment_row align-items-start">
                            <div class="me-4">
                                {!! Form::label("paid_on", __('lang_v1.paid_on') . ':*') !!}
                                <div class="input-group ms-2">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('paid_on', @format_datetime(\Carbon::now()), ['class' => 'form-control paid_on', 'readonly', 'required']) !!}
                                </div>
                            </div>
                            <div class="me-4 w-200px">
                                {!! Form::label("method", __('purchase.payment_method') . ':*') !!}
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
                    </form>
                    @endcomponent
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='save_multi_pay'>@lang('messages.save')</button>
                    <button type="button" class="btn btn-default btn-close"
                        data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                var sel_currency_symbol = '';

                function calc_total_payAmount() {
                    let total_amount = 0;
                    const payInputs = document.querySelectorAll("table#multi-pay-table tbody input.pay-amount");
                    payInputs.forEach(function (payInput) {
                        total_amount += parseFloat(payInput.value);
                    })
                    $("input[name='multi-pay-total-amount']").val(floatV(total_amount));
                }

                $(document).off('click', 'button.add-multi-pay');
                $(document).on('click', 'button.add-multi-pay', function (e) {
                    const transaction_id = $(e.target).data("sell-id");
                    const sell_due_tr = e.target.closest('tr');

                    // To check currency is the same as the selected currency
                    const currency_symbol = $(sell_due_tr.querySelector("td:nth-child(8)")).text();
                    if (sel_currency_symbol && sel_currency_symbol != currency_symbol) {
                        toastr.warning("Could not add transaction with the different currency!");
                        return;
                    }
                    if (!sel_currency_symbol) sel_currency_symbol = currency_symbol;

                    // To check whether this sell was inclued in multi-pay
                    const existing = $(`table#multi-pay-table tr[data-sell-id='${transaction_id}']`);
                    if (existing.length > 0) {
                        toastr.warning('This sell has already been added.');
                        return;
                    }

                    const multi_pay_tablebody = $("table#multi-pay-table tbody")[0];
                    const newTr = document.createElement('tr');
                    $(newTr).attr('data-sell-id', transaction_id);

                    // action
                    let td = document.createElement("td");
                    let html = "<div class='btn-group d-flex justify-content-center'>";
                    html += "<button class='btn btn-danger btn-xs remove-multi-pay'><i class='fa fa-trash' aria-hidden='true'></i>Remove</button>";
                    html += "</div>";
                    $(td).html(html);
                    newTr.appendChild(td);

                    // transaction date
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(2)").cloneNode(true));

                    // sell invoce_no
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(3)").cloneNode(true));

                    // customer_name
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(4)").cloneNode(true));

                    // sale.location
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(5)").cloneNode(true));

                    // sale.payment_status
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(6)").cloneNode(true));

                    // invoice.document_type
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(7)").cloneNode(true));

                    // currency
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(8)").cloneNode(true));

                    // total_amount
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(9)").cloneNode(true));

                    // total_paid
                    newTr.appendChild(sell_due_tr.querySelector("td:nth-child(10)").cloneNode(true));

                    // sell_due
                    td = sell_due_tr.querySelector("td:nth-child(11)");
                    let sell_due = parseFloat($(td).find(".payment_due").data("orig-value"));
                    if (!sell_due) sell_due = parseFloat($(td).find(".payment_due").data("orig-value-currency2"));
                    newTr.appendChild(td.cloneNode(true));

                    // pay amount
                    td = document.createElement("td");
                    html = "<div class='d-flex justify-content-center'>";
                    html += `<input type='number' class='form-control pay-amount' data-sell-id='${transaction_id}' data-sell-due='${sell_due}' value='${sell_due}'/>`;
                    html += "</div>";
                    $(td).html(html);
                    newTr.appendChild(td);

                    multi_pay_tablebody.appendChild(newTr);

                    setTimeout(() => {
                        calc_total_payAmount();
                    }, 200);
                })

                $(document).off('click', 'button.remove-multi-pay');
                $(document).on('click', 'button.remove-multi-pay', function (e) {
                    const tr = e.target.closest('tr');
                    tr.remove();

                    // To check whether payments table is empty.
                    const trs = $("#multi-pay-table tbody tr");
                    if (trs.length == 0) sel_currency_symbol = '';

                    setTimeout(() => {
                        calc_total_payAmount();
                    }, 200);
                })

                $(document).off('change', 'input.pay-amount');
                $(document).on('change', 'input.pay-amount', function (e) {
                    const sell_due = $(e.target).data("sell-due");
                    if (sell_due < e.target.value) {
                        toastr.warning("Please input value less than sell due.");
                        $(e.target).val(sell_due);
                    }

                    setTimeout(() => {
                        calc_total_payAmount();
                    }, 200);
                })

                $(document).off('click', 'button#save_multi_pay');
                $(document).on('click', 'button#save_multi_pay', function (e) {
                    $("form#payment_form").trigger('submit');
                })

                $("form#payment_form")
                    .submit(function (e) {
                        e.preventDefault();
                    })
                    .validate({
                        submitHandler: function (form) {
                            // prepare post data
                            const data = new FormData();
                            data.append('contact_id', $("input[name='contact_id']").val());
                            data.append('total_amount', $("input[name='multi-pay-total-amount']").val());
                            data.append('transaction_type', 'sell');

                            const paymentForm = $("form#payment_form");
                            paymentForm.serializeArray().forEach(item => {
                                data.append(item.name, item.value)
                            });

                            const pays = [];
                            const payInputs = document.querySelectorAll("table#multi-pay-table tbody input.pay-amount");
                            if (payInputs.length === 0) {
                                toastr.warning('Please select at lease one transaction.');
                                return;
                            }
                            payInputs.forEach(payInput => {
                                pays.push({
                                    transaction_id: $(payInput).data("sell-id"),
                                    amount: payInput.value
                                });
                            });
                            data.append('pays', JSON.stringify(pays));

                            // document file
                            const documentFile = $("#document");
                            if (documentFile[0].files.length > 0) {
                                data.set("document", documentFile[0].files[0]);
                            }

                            $("button#save_multi_pay").prop("disabled", true);
                            const url = paymentForm.data('action');
                            $.ajax({
                                method: "post",
                                url: url,
                                dataType: "json",
                                data,
                                processData: false,
                                contentType: false,
                                success: function (result) {
                                    $("button#save_multi_pay").prop("disabled", false);
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        $("button.btn-close").trigger('click');
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                }
                            });
                        }
                    })

                var sell_due_table = $('#sell_due_table').DataTable({
                    processing: true,
                    serverSide: true,
                    aaSorting: [[1, 'desc']],
                    "ajax": {
                        "url": "/sells/ondue/" + $("input[name='contact_id']").val(),
                        "data": function (d) {
                        }
                    },
                    scrollY: "20vh",
                    scrollX: true,
                    scrollCollapse: true,
                    columns: [
                        { data: 'action', name: 'action', orderable: false, "searchable": false },
                        { data: 'transaction_date', name: 'transaction_date' },
                        { data: 'invoice_no', name: 'invoice_no' },
                        { data: 'contact_name', name: 'contacts.supplier_business_name' },
                        { data: 'business_location', name: 'bl.name' },
                        { data: 'payment_status', name: 'payment_status' },
                        { data: 'document_type', orderable: false, "searchable": false },
                        { data: 'currency', name: 'currency_id', "searchable": false },
                        { data: 'final_total', name: 'final_total' },
                        { data: 'total_paid', name: 'total_paid', "searchable": false },
                        { data: 'total_remaining', name: 'total_remaining' },
                        { data: 'return_due', orderable: false, "searchable": false },
                        { data: 'tax_amount', name: 'tax_amount', "searchable": false },
                        { data: 'exchange_rate', name: 'exchange_rate', "searchable": false },
                    ],
                    "fnDrawCallback": function (oSettings) {
                        // __currency_convert_recursively($('#sell_table'));
                    }
                });
            })
        </script>