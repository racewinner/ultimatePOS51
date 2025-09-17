<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- <link rel="stylesheet" href="style.css"> -->
	<title>Receipt-{{$receipt_details->invoice_no}}</title>
	<style>
		body {
			font-size: 12px;
			padding: 5px !important;
		}
		.text-right {
			text-align: right !important;
		}
		.w-100 {
			width: 100% !important;
		}
	</style>
</head>

<body>
	<table style="width:100%;">
		<thead>
			<tr>
				<td>
					@if(!empty($receipt_details->invoice_heading))
						<div class="text-right text-muted-imp" style="font-weight: bold;">{!! $receipt_details->invoice_heading !!}</div>
					@endif

					<div class="text-right">
						@if(!empty($receipt_details->invoice_no_prefix))
							{!! $receipt_details->invoice_no_prefix !!}
						@endif

						{{$receipt_details->invoice_no}}
					</div>
				</td>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>

	@if(!empty($receipt_details->header_text))
		<div class="">
			<div>
				{!! $receipt_details->header_text !!}
			</div>
		</div>
	@endif

	<!-- business information here -->
	<div class="">
		<div class="color-555">
			<!-- Logo -->
			@if(!empty($receipt_details->logo))
				<img src="{{$receipt_details->logo}}" class="img" style="width:100px;height:100px;" >
				<br/>
			@endif

			<!-- Shop & Location Name  -->
			@if(!empty($receipt_details->display_name))
				<div>
					{{$receipt_details->display_name}}<br/>
					{!! $receipt_details->address !!}

					@if(!empty($receipt_details->contact))
						<br/><span>{!! $receipt_details->contact !!}</span>
					@endif

					@if(!empty($receipt_details->website))
						<br/>{{ $receipt_details->website }}
					@endif

					@if(!empty($receipt_details->tax_info1))
						<br/>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
					@endif

					@if(!empty($receipt_details->tax_info2))
						<br/>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
					@endif

					@if(!empty($receipt_details->location_custom_fields))
						<br/>{{ $receipt_details->location_custom_fields }}
					@endif
				</div>
			@endif

			<!-- Table information-->
			@if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
				<div>
					@if(!empty($receipt_details->table_label))
						{!! $receipt_details->table_label !!}
					@endif
					{{$receipt_details->table}}
				</div>
			@endif

			<!-- Waiter info -->
			@if(!empty($receipt_details->waiter_label) || !empty($receipt_details->waiter))
				<div>
					@if(!empty($receipt_details->waiter_label))
						{!! $receipt_details->waiter_label !!}
					@endif
					{{$receipt_details->waiter}}
				</div>
			@endif
		</div>

		<div style="margin-top: 20px;">
			<div class="text-right">
				@if(!empty($receipt_details->invoice_no_prefix))
					<span class="pull-left">{!! $receipt_details->invoice_no_prefix !!}</span>
				@endif
				{{$receipt_details->invoice_no}}
			</div>

			<div class="text-right">
				@if(!empty($receipt_details->parent_invoice_no_prefix))
					<span class="pull-left">{!! $receipt_details->parent_invoice_no_prefix !!}</span>
				@endif
				{{$receipt_details->parent_invoice_no}}
			</div>

			{{--
				<table class="table table-condensed">
					@if(!empty($receipt_details->payments))
						@foreach($receipt_details->payments as $payment)
							<tr>
								<td>{{$payment['method']}}</td>
								<td>{{$payment['amount']}}</td>
							</tr>
						@endforeach
					@endif
				</table>
			--}}

			<!-- Total Due-->
			@if(!empty($receipt_details->total_due))
				<div class="bg-light-blue-active text-right padding-5">
					<span class="pull-left bg-light-blue-active">
						{!! $receipt_details->total_due_label !!}
					</span>

					{{$receipt_details->total_due}}
				</div>
			@endif
			
			<!-- Total Paid-->
			@if(!empty($receipt_details->total_paid))
				<div class="text-right color-555">
					<span class="pull-left">{!! $receipt_details->total_paid_label !!}</span>
					{{$receipt_details->total_paid}}
				</div>
			@endif

			<!-- Date-->
			@if(!empty($receipt_details->date_label))
				<div class="text-right color-555">
					<span class="pull-left">
						{{$receipt_details->date_label}}
					</span>
					{{$receipt_details->invoice_date}}
				</div>
			@endif

			<!-- Causer-->
			@if(!empty($causer))
				<div class="text-right color-555">
					<span class="pull-left">@lang('lang_v1.user'):</span>
					{{$causer->first_name}} {{$causer->last_name}}
				</div>
			@endif
		</div>
	</div>

	<div class="color-555">
		<br/>
		<div class="word-wrap">
			<b>{{ $receipt_details->customer_label ?? '' }}</b><br/>

			<!-- customer info -->
			@if(!empty($receipt_details->customer_name))
				{!! $receipt_details->customer_name !!}<br>
			@endif
			@if(!empty($receipt_details->customer_info))
				{!! $receipt_details->customer_info !!}
			@endif
			@if(!empty($receipt_details->client_id_label))
				<br/>
				{{ $receipt_details->client_id_label }} {{ $receipt_details->client_id }}
			@endif
			@if(!empty($receipt_details->customer_tax_label))
				<br/>
				{{ $receipt_details->customer_tax_label }} {{ $receipt_details->customer_tax_number }}
			@endif
			@if(!empty($receipt_details->customer_custom_fields))
				<br/>{!! $receipt_details->customer_custom_fields !!}
			@endif
		</div>

		
		<div class="word-wrap">
			<div>
				@if(!empty($receipt_details->sub_heading_line1))
					{{ $receipt_details->sub_heading_line1 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line2))
					<br>{{ $receipt_details->sub_heading_line2 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line3))
					<br>{{ $receipt_details->sub_heading_line3 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line4))
					<br>{{ $receipt_details->sub_heading_line4 }}
				@endif		
				@if(!empty($receipt_details->sub_heading_line5))
					<br>{{ $receipt_details->sub_heading_line5 }}
				@endif
			</div>
		</div>
		
	</div>

	<div class="row color-555">
		<div>
			<br/>
			<table style='font-size:10px; width:100%'>
				<thead>
					<tr class="table-no-side-cell-border table-no-top-cell-border text-center">
						@php
							$p_width = 40;
						@endphp
						@if($receipt_details->show_cat_code != 1)
							@php
								$p_width = 50;
							@endphp
						@endif
						<td style="width: {{$p_width}}% !important">
							{{$receipt_details->table_product_label}}
						</td>

						@if($receipt_details->show_cat_code == 1)
							<td style="width: 10% !important">{{$receipt_details->cat_code_label}}</td>
						@endif
						
						<td style="width: 15% !important" class="text-right">
							{{$receipt_details->table_qty_label}}
						</td>
						<td style="width: 15% !important" class="text-right">
							{{$receipt_details->table_unit_price_label}}
						</td>
						<td style="width: 20% !important" class="text-right">
							{{$receipt_details->table_subtotal_label}}
						</td>
					</tr>
				</thead>
				<tbody>
					@foreach($receipt_details->lines as $line)
						<tr>
							<td>
								{{$line['name']}} {{$line['variation']}} 
								@if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif
								@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							</td>

							@if($receipt_details->show_cat_code == 1)
								<td>
									@if(!empty($line['cat_code']))
										{{$line['cat_code']}}
									@endif
								</td>
							@endif

							<td class="text-right">
								{{$line['quantity']}} {{$line['units']}}
							</td>
							<td class="text-right">
								{{$line['unit_price_exc_tax']}}
							</td>
							<td class="text-right">
								{{$line['line_total']}}
							</td>
						</tr>
					@endforeach

					@php
						$lines = count($receipt_details->lines);
					@endphp
				</tbody>
			</table>
		</div>
	</div>

	<div class="color-555" style="page-break-inside: avoid !important; margin-bottom:20px; margin-top:20px;">
		<div class=" ">
			<b class="pull-left">Authorized Signatory</b>
		</div>

		<div class=" ">
			<table class="table-no-side-cell-border table-no-top-cell-border w-100">
				<tbody>
					<tr class="color-555">
						<td style="width:50%">
							{!! $receipt_details->subtotal_label !!}
						</td>
						<td class="text-right">
							{{$receipt_details->subtotal}}
						</td>
					</tr>

					<!-- Tax -->
					@if(!empty($receipt_details->taxes))
						@foreach($receipt_details->taxes as $k => $v)
							<tr class="color-555">
								<td>{{$k}}</td>
								<td class="text-right">{{$v}}</td>
							</tr>
						@endforeach
					@endif

					<!-- Discount -->
					@if( !empty($receipt_details->discount) )
						<tr class="color-555">
							<td>
								{!! $receipt_details->discount_label !!}
							</td>

							<td class="text-right">
								(-) {{$receipt_details->discount}}
							</td>
						</tr>
					@endif

					@if(!empty($receipt_details->group_tax_details))
						@foreach($receipt_details->group_tax_details as $key => $value)
							<tr class="color-555">
								<td>
									{!! $key !!}
								</td>
								<td class="text-right">
									(+) {{$value}}
								</td>
							</tr>
						@endforeach
					@else
						@if( !empty($receipt_details->tax) )
							<tr class="color-555">
								<td>
									{!! $receipt_details->tax_label !!}
								</td>
								<td class="text-right">
									(+) {{$receipt_details->tax}}
								</td>
							</tr>
						@endif
					@endif
					
					<!-- Total -->
					<tr>
						<td>
							{!! $receipt_details->total_label !!}
						</td>
						<td class="text-right">
							{{$receipt_details->total}}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row color-555">
		<div>
			{{$receipt_details->additional_notes}}
		</div>

		{{-- Barcode --}}
		@if($receipt_details->show_barcode)
			<div>
				<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
			</div>
		@endif
	</div>

	@if(!empty($receipt_details->footer_text))
		<div class="row color-555">
			<div>
				{!! $receipt_details->footer_text !!}
			</div>
		</div>
	@endif
				</td>
			</tr>
		</tbody>
	</table>
	
	@if( !empty($pin_user) )
	<div style="display:flex; justify-content:end; margin-top: 20px;">
		Authorized by {{ $pin_user->first_name }} {{ $pin_user->last_name }}
	</div>
	@endif
</body>
</html>
