@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
	<h1>@lang('lang_v1.sell_return')</h1>
</section>

<!-- Main content -->
<section class="content no-print">

	{!! Form::hidden('location_id', $sell->location->id, ['id' => 'location_id', 'data-receipt_printer_type' => $sell->location->receipt_printer_type]) !!}

	{!! Form::open(['url' => action([\App\Http\Controllers\SellReturnController::class, 'store']), 'method' => 'post', 'id' => 'sell_return_form']) !!}
	{!! Form::hidden('transaction_id', $sell->id) !!}
	{!! Form::hidden('sell_return_id', $sell_return ? $sell_return->id : '') !!}
	{!! Form::hidden('contact_id', $sell->contact_id) !!}
	{!! Form::hidden('invoice_no', $sell->invoice_no) !!}
	{!! Form::hidden('tax_calculation_amount', isset($sell->tax->amount) ? $sell->tax->amount : '0') !!}
	{!! Form::hidden('pin', "") !!}
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">@lang('lang_v1.parent_sale')</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<strong>@lang('sale.invoice_no'):</strong> {{ $sell->invoice_no }} <br>
					<strong>@lang('messages.date'):</strong> {{@format_date($sell->transaction_date)}}
				</div>
				<div class="col-sm-4">
					<strong>@lang('contact.customer'):</strong> {{ !empty($sell->contact->name) ? $sell->contact->name : $sell->contact->supplier_business_name }} <br>
					<strong>@lang('purchase.business_location'):</strong> {{ $sell->location->name }}
				</div>
			</div>
		</div>
	</div>
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('invoice_no', __('sale.invoice_no') . ':') !!}
						{!! Form::text('invoice_no', !empty($sell->return_parent->invoice_no) ? $sell->return_parent->invoice_no : null, ['class' => 'form-control']) !!}
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							@php
								$transaction_date = !empty($sell->return_parent->transaction_date) ? $sell->return_parent->transaction_date : 'now';
							@endphp
							{!! Form::text('transaction_date', @format_datetime($transaction_date), ['class' => 'form-control', 'readonly', 'required']) !!}
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label("pdf_format", __('invoice.invoice_pdf_format')) !!}
						{!! Form::select('pdf_format', $pdf_formats, 'ticket', ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('note', __('sale.return_note') . ':*') !!}
						{!! Form::text('note', !empty($sell_return->additional_notes) ? $sell_return->additional_notes : null, ['class' => 'form-control']) !!}
					</div>
				</div>
				<div class="col-sm-12">
					<table class="table bg-gray" id="sell_return_table">
						<thead>
							<tr class="bg-green">
								<th>#</th>
								<th>@lang('product.product_name')</th>
								<th>@lang('sale.unit_price')</th>
								<th>@lang('lang_v1.sell_quantity')</th>
								<th>@lang('lang_v1.return_quantity')</th>
								<th>@lang('lang_v1.return_subtotal')</th>
							</tr>
						</thead>
						<tbody>
							@foreach($sell->sell_lines as $sell_line)
								@php
									$check_decimal = 'false';
									if ($sell_line->product->unit->allow_decimal == 0) {
										$check_decimal = 'true';
									}

									$unit_name = $sell_line->product->unit->short_name;

									if (!empty($sell_line->sub_unit)) {
										$unit_name = $sell_line->sub_unit->short_name;

										if ($sell_line->sub_unit->allow_decimal == 0) {
											$check_decimal = 'true';
										} else {
											$check_decimal = 'false';
										}
									}

								@endphp
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>
										{{ $sell_line->product->name }}
										@if($sell_line->product->type == 'variable')
											- {{ $sell_line->variations->product_variation->name}}
											- {{ $sell_line->variations->name}}
										@endif
										<br>
										{{ $sell_line->variations->sub_sku }}
									</td>
									<td>
										{{ \App\Utils\Util::format_currency($sell_line->unit_price_inc_tax, $sell->currency) }}
									</td>
									<td>{{ $sell_line->formatted_qty }} {{$unit_name}}</td>

									<td>
										<input type="text" name="products[{{$loop->index}}][quantity]"
											value="{{@format_quantity($sell_line->quantity_returned)}}"
											class="form-control input-sm input_number return_qty input_quantity"
											data-rule-abs_digit="{{$check_decimal}}"
											data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
											data-rule-max-value="{{$sell_line->quantity}}"
											data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty' => $sell_line->formatted_qty, 'unit' => $unit_name])">
										<input name="products[{{$loop->index}}][unit_price_inc_tax]" type="hidden"
											class="unit_price" value="{{@num_format($sell_line->unit_price_inc_tax)}}">
										<input name="products[{{$loop->index}}][sell_line_id]" type="hidden"
											value="{{$sell_line->id}}">
										<input name="products[{{$loop->index}}][id]" type="hidden"
											value="{{$sell_line->product->id}}">
										<input name="products[{{$loop->index}}][tax_id]" type="hidden"
											value="{{$sell_line->tax_id}}">
									</td>
									<td>
										<div class="return_subtotal"></div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				@php
					$discount_type = !empty($sell->return_parent->discount_type) ? $sell->return_parent->discount_type : $sell->discount_type;
					$discount_amount = !empty($sell->return_parent->discount_amount) ? $sell->return_parent->discount_amount : $sell->discount_amount;
				@endphp
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('discount_type', __('purchase.discount_type') . ':') !!}
						{!! Form::select('discount_type', ['' => __('lang_v1.none'), 'fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type, ['class' => 'form-control']) !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
						{!! Form::text('discount_amount', @num_format($discount_amount), ['class' => 'form-control input_number']) !!}
					</div>
				</div>
			</div>
			@php
				$tax_percent = 0;
				if (!empty($sell->tax)) {
					$tax_percent = $sell->tax->amount;
				}
			@endphp
			{!! Form::hidden('tax_id', $sell->tax_id) !!}
			{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']) !!}
			{!! Form::hidden('tax_percent', $tax_percent, ['id' => 'tax_percent']) !!}
			<div class="row">
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.total_return_discount'):</strong>
					&nbsp;(-) <span id="total_return_discount"></span>
				</div>
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.total_return_tax') - @if(!empty($sell->tax))({{$sell->tax->name}} -
					{{$sell->tax->amount}}%)@endif : </strong>
					&nbsp;(+) <span id="total_return_tax"></span>
				</div>
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.return_total'): </strong>&nbsp;
					<span id="net_return">0</span>
				</div>
			</div>
			@include('sell_return.partials.electronic_bill')
			<br>
			<div class="row">
				<div class="col-sm-12 d-flex justify-content-end">
          <button type="button" class="btn btn-default back me-4" >@lang('lang_v1.go_back')</button>
					<button type="button" class="btn btn-primary pull-right" id="btn_save_return">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}

</section>

@include('sell_return.partials.confirm_pin_modal')

@stop
@section('javascript')
	@yield('bill_javascript')
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/sell_return.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			const sell_currency = @json($sell->currency);
			__p_currency_symbol = sell_currency.symbol;
			__p_currency_thousand_separator = sell_currency.thousand_separator;
			__p_currency_decimal_separator = sell_currency.decimal_separator;

			$('form#sell_return_form').validate();
			update_sell_return_total();

			//Date picker
			// $('#transaction_date').datepicker({
			//     autoclose: true,
			//     format: datepicker_date_format
			// });
		});
		$(document).on('change', 'input.return_qty, #discount_amount, #discount_type', function () {
			update_sell_return_total()
		});

		function update_sell_return_total() {
			var net_return = 0;
			$('table#sell_return_table tbody tr').each(function () {
				var quantity = __read_number($(this).find('input.return_qty'));
				var unit_price = __read_number($(this).find('input.unit_price'));
				var subtotal = quantity * unit_price;
				$(this).find('.return_subtotal').text(__currency_trans_from_en(subtotal, true, true));
				net_return += subtotal;
			});
			var discount = 0;
			if ($('#discount_type').val() == 'fixed') {
				discount = __read_number($("#discount_amount"));
			} else if ($('#discount_type').val() == 'percentage') {
				var discount_percent = __read_number($("#discount_amount"));
				discount = __calculate_amount('percentage', discount_percent, net_return);
			}
			discounted_net_return = net_return - discount;

			var tax_percent = $('input#tax_percent').val();
			var total_tax = __calculate_amount('percentage', tax_percent, discounted_net_return);
			var net_return_inc_tax = total_tax + discounted_net_return;

			$('input#tax_amount').val(total_tax);
			$('span#total_return_discount').text(__currency_trans_from_en(discount, true, true));
			$('span#total_return_tax').text(__currency_trans_from_en(total_tax, true, true));
			$('span#net_return').text(__currency_trans_from_en(net_return_inc_tax, true, true));
		}

		$("button#btn_save_return").on('click', function () {
			const enable_pin = {{ !empty($enable_pin) ? 'true' : 'false'  }};
			if (enable_pin) {
				$("#modal_pin_confirm").modal("show");
			} else {
				const form = $("form#sell_return_form");
				form.trigger('submit');
			}
		})

		$("button#confirm_pin").on('click', function () {
			const input_pin = $("input[name='input_pin']").val();
			if (!input_pin) {
				toastr.warning("Please input PIN.");
				return;
			}

			$("#modal_pin_confirm").modal("hide");
			$("input[name='pin']").val(input_pin);
			$("input[name='input_pin']").val('');

			const form = $("form#sell_return_form");
			form.trigger('submit');
		})
	</script>
@endsection