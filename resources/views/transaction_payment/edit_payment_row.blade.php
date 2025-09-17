<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'update'], [$payment_line->id]), 'method' => 'put', 'id' => 'transaction_payment_add_form', 'files' => true]) !!}
    {!! Form::hidden('default_payment_accounts', !empty($transaction->location) ? $transaction->location->default_payment_accounts : '[]', ['id' => 'default_payment_accounts']) !!}
    {!! Form::hidden('payment_id', $payment_line->id, ['id' => 'payment_id']) !!}
    {!! Form::hidden('transaction_id', $payment_line->transaction_id, ['id' => 'transaction_id']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('purchase.edit_payment')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if(!empty($transaction->contact))
      <div class="col-md-4">
        <div class="well">
        <strong>@if($transaction->contact->type == 'supplier') @lang('purchase.supplier'): @else
      @lang('contact.customer'): @endif </strong>{{ $transaction->contact->full_name_with_business }}<br>
        <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
        </div>
      </div>
    @endif
        @if($transaction->type != 'opening_balance')
        <div class="col-md-4">
          <div class="well">
          <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}<br>
          @if(!empty($transaction->location))
        <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
        @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="well">
          <strong>@lang('sale.total_amount'): </strong><span class="display_currency"
            data-currency_symbol="true">{{ $transaction->final_total }}</span><br>
          <strong>@lang('purchase.payment_note'): </strong>
          @if(!empty($transaction->additional_notes))
        {{ $transaction->additional_notes }}
        @else
        --
        @endif
          </div>
        </div>
    @endif
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method", __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on", __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control', 'readonly', 'required']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount", __('sale.amount') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => 'Amount']) !!}
            </div>
          </div>
        </div>
        @if(empty($transaction))
      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('currency_id', __('business.currency') . ':*') !!}
        <div class="input-group">
          <span class="input-group-addon">
          <i class="fas fa-money-bill-alt"></i>
          </span>
          <select name='currency_id' class='form-control select2' required>
          @foreach ($currencies as $currency)
        <option value={{ $currency->id }} {{ $currency->id == $payment_line->currency_id ? "selected" : "" }}
        data-thousand-separator={{ empty($currency->thousand_separator) ? "NA" : $currency->thousand_separator }} data-decimal-separator={{ empty($currency->decimal_separator) ? "NA" : $currency->decimal_separator}} data-symbol={{ $currency->symbol }}>
        {{ $currency->name }}
        </option>
      @endforeach
          </select>
        </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('exchange_rate', __('lang_v1.currency_exchange_rate') . ':*') !!}
        <div class="input-group">
          <span class="input-group-addon">
          <i class="fas fa-money-bill-alt"></i>
          </span>
          {!! Form::number('nation_exchg_rate', $payment_line->nation_exchg_rate, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.currency_exchange_rate')]) !!}
        </div>
        </div>
      </div>
    @endif

        @php
      $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];

      $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
    @endphp

        @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens')
        <input type="hidden" class="enable_cash_denomination_for_payment_methods"
          value="{{json_encode($pos_settings['enable_cash_denomination_for_payment_methods'])}}">
        <div class="clearfix"></div>
        <div
          class="col-md-12 cash_denomination_div @if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
          <hr>
          <strong>@lang('lang_v1.cash_denominations')</strong>
          @if(!empty($pos_settings['cash_denominations']))
          <table class="table table-slim">
          <thead>
          <tr>
          <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
          <th width="20%">&nbsp;</th>
          <th width="20%" class="text-center">@lang('lang_v1.count')</th>
          <th width="20%">&nbsp;</th>
          <th width="20%" class="text-left">@lang('sale.subtotal')</th>
          </tr>
          </thead>
          <tbody>
          @php
        $total = 0;
        @endphp
          @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
          @php
          $count = 0;
          $sub_total = 0;
          foreach ($payment_line->denominations as $d) {
          if ($d->amount == $dnm) {
          $count = $d->total_count;
          $sub_total = $d->total_count * $d->amount;
          $total += $sub_total;
          }
          }
          @endphp
          <tr>
          <td class="text-right">{{$dnm}}</td>
          <td class="text-center">X</td>
          <td>
          {!! Form::number("denominations[$dnm]", $count, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;']) !!}
          </td>
          <td class="text-center">=</td>
          <td class="text-left">
          <span class="denomination_subtotal">{{@num_format($sub_total)}}</span>
          </td>
          </tr>
        @endforeach
          </tbody>
          <tfoot>
          <tr>
          <th colspan="4" class="text-center">@lang('sale.total')</th>
          <td>
            <span class="denomination_total">{{@num_format($total)}}</span>
            <input type="hidden" class="denomination_total_amount" value="{{$total}}">
            <input type="hidden" class="is_strict"
            value="{{$pos_settings['cash_denomination_strict_check'] ?? ''}}">
          </td>
          </tr>
          </tfoot>
          </table>
          <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
        @else
        <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
        @endif
        </div>
        <div class="clearfix"></div>
    @endif
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]) !!}
            <p class="help-block">@lang('lang_v1.previous_file_will_be_replaced')
              @includeIf('components.document_help_text')</p>
          </div>
        </div>
        @if(!empty($accounts))
      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label("account_id", __('lang_v1.payment_account') . ':') !!}
        <div class="input-group">
          <span class="input-group-addon">
          <i class="fas fa-money-bill-alt"></i>
          </span>
          {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '', ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']) !!}
        </div>
        </div>
      </div>
    @endif

        <div class="clearfix"></div>
        @include('transaction_payment.payment_type_details')
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]) !!}
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12 invoice_container'>
          @if($payment_line->pymo_invoice)
        <i class='fa fa-download'></i>
        <a class='m-2' href='/uploads/invoices/{{$payment_line->business_id}}/{{$payment_line->pymo_invoice}}.pdf'
        target='_blank'>@lang('invoice.download_document')</a>
      @else
        <button class="btn btn-default" id='generate_invoice_btn'>@lang('invoice.generate_invoice')</button>
      @endif
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $(document).ready(function () {
    $('#generate_invoice_btn').on('click', function (e) {
      e.preventDefault();
      $('#generate_invoice_btn').attr('disabled', true);

      var formData = $("form#transaction_payment_add_form").serializeArray().reduce(function (obj, item) {
        obj[item.name] = item.value;
        return obj;
      }, {});
      delete formData._method;
      formData.payment_id = $("#payment_id").val();
      formData.transaction_id = $("#transaction_id").val();

      $.post('/pymo/sendReceiptInvoice', formData, function (response) {
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
      }).fail(function (xhr, status, error) {
        console.error(error);
        $('#generate_invoice_btn').attr('disabled', false);
      });
    })
  });
</script>