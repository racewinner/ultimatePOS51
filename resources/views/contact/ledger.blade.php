<!-- app css -->

@if(!empty($for_pdf))
	<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
@endif
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 align-right @endif">
        <p class="text-right align-right"><strong>{{$contact->business->name}}</strong>
        	<br>
        	@if(!empty($location))
        		{!! $location->location_address !!}
        	@else
        		{!! $contact->business->business_address !!}
        	@endif
        </p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class="blue-heading p-4 width-50">@lang('lang_v1.to'):</p>
	<p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!} @if(!empty($contact->email)) <br>@lang('business.email'): {{$contact->email}} @endif
	<br>@lang('contact.mobile'): {{$contact->mobile}}
	@if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}} @endif
</p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<h3 class="mb-0 blue-heading p-4">@lang('lang_v1.account_summary')</h3>
	<small>{{$ledger_details['start_date']}} @lang('lang_v1.to') {{$ledger_details['end_date']}}</small>
	<hr>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('lang_v1.opening_balance')</td>
			<td class="align-right">
				{{ \App\Utils\Util::format_currency($ledger_details['beginning_balance'], $business->currency) }}
				@if($business->second_currency_id > 0)
				 / 
				{{ \App\Utils\Util::format_currency($ledger_details['beginning_balance_currency2'], $business->secondCurrency) }}
				@endif
			</td>
		</tr>
	@if( $contact->type == 'supplier' || $contact->type == 'both')
		<tr>
			<td>@lang('report.total_purchase')</td>
			<td class="align-right">
				{{ \App\Utils\Util::format_currency($ledger_details['total_purchase'], $business->currency) }}
				@if($business->second_currency_id > 0)
				 / 
				 {{ \App\Utils\Util::format_currency($ledger_details['total_purchase_currency2'], $business->secondCurrency) }} 
				@endif
			</td>
		</tr>
	@endif
	@if( $contact->type == 'customer' || $contact->type == 'both')
		<tr>
			<td>@lang('lang_v1.total_invoice')</td>
			<td class="align-right">
				{{ \App\Utils\Util::format_currency($ledger_details['total_invoice'], $business->currency) }}
				@if($business->second_currency_id > 0)
				 / 
				 {{ \App\Utils\Util::format_currency($ledger_details['total_invoice_currency2'], $business->secondCurrency) }}
				@endif
			</td>
		</tr>
	@endif
	<tr>
		<td>@lang('sale.total_paid')</td>
		<td class="align-right">
			{{ \App\Utils\Util::format_currency($ledger_details['total_paid'], $business->currency) }}
			@if($business->second_currency_id > 0)
			 / 
			{{ \App\Utils\Util::format_currency($ledger_details['total_paid_currency2'], $business->secondCurrency) }}
			@endif
		</td>
	</tr>
	<tr>
		<td>@lang('lang_v1.advance_balance')</td>
		<td class="align-right">
			{{ \App\Utils\Util::format_currency($ledger_details['balance_due'] > 0 ? 0 : abs($ledger_details['balance_due']), $business->currency) }}			
			@if($business->second_currency_id > 0)
			 / 
			 {{ \App\Utils\Util::format_currency($ledger_details['balance_due_currency2'] > 0 ? 0 : abs($ledger_details['balance_due_currency2']), $business->secondCurrency) }}
			@endif
		</td>
	</tr>
	@if($ledger_details['ledger_discount'] > 0 || $ledger_details['ledger_discount_currency2'] > 0)
		<tr>
			<td>@lang('lang_v1.ledger_discount')</td>
			<td class="align-right">
				{{ \App\Utils\Util::format_currency($ledger_details['ledger_discount'], $business->currency) }}
				@if($business->second_currency_id > 0)
				 / 
				{{ \App\Utils\Util::format_currency($ledger_details['ledger_discount_currency2'], $business->secondcurrency) }}
				@endif
			</td>
		</tr>
	@endif
	<tr>
		<td><strong>@lang('lang_v1.balance_due')</strong></td>
		<td class="align-right">
			{{ \App\Utils\Util::format_currency($ledger_details['balance_due'] - $ledger_details['ledger_discount'], $business->currency) }}
			@if($business->second_currency_id > 0)
			 / 
			{{ \App\Utils\Util::format_currency($ledger_details['balance_due_currency2'] - $ledger_details['ledger_discount_currency2'], $business->secondCurrency) }}
			@endif
		</td>
	</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p class="text-center" style="text-align: center;"><strong>@lang('lang_v1.ledger_table_heading', ['start_date' => $ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
	<div class="table-responsive">
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border blue-heading">
				<th width="18%" class="text-center">@lang('lang_v1.date')</th>
				<th width="12%" class="text-center">eFactura</th>
				<th width="28%" class="text-center">Tipo Doc</th>
				<th width="9%" class="text-center">@lang('purchase.ref_no')</th>
				<th width="8%" class="text-center">@lang('lang_v1.type')</th>
				<!--<th width="10%" class="text-center">@lang('sale.location')</th>-->
				<th width="5%" class="text-center">@lang('sale.payment_status')</th>
				<th width="10%" class="text-center">@lang('account.debit')</th>
				<th width="10%" class="text-center">@lang('account.credit')</th>
				<th width="10%" class="text-center">@lang('lang_v1.balance')</th>
				<th width="5%" class="text-center">@lang('lang_v1.payment_method')</th>
				<th width="15%" class="text-center">@lang('report.others')</th>
			</tr>
		</thead>
		<tbody>
			@php
				$total_due_sum = 0; 
				$total_due_sum_currency2 = 0;
			@endphp
			@foreach($ledger_details['ledger'] as $data)
				<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
					<td class="row-border">{{@format_datetime($data['date'])}}</td>
					<td class=''>{{$data['pymo_serie']}}</td>
					<td class='text-center'>{{$data['document_type']}}</td>
					<td class='text-center'>{{$data['ref_no']}}</td>
					<td class=''>{{$data['type']}}</td>
					<!--<td class=''>{{$data['location']}}</td>-->
					<td class='text-center'>{{$data['payment_status']}}</td>
					<td class="ws-nowrap text-center">
						@if($data['debit'] > 0)
						{{ \App\Utils\Util::format_currency($data['debit'], $business->currency) }}
						@endif
						@if($business->second_currency_id > 0 && $data['debit_currency2'] > 0)
						{{ ($data['debit'] > 0) ? '/' : '' }}
						{{ \App\Utils\Util::format_currency($data['debit_currency2'], $business->secondCurrency) }}
						@endif
					</td>
					<td class="ws-nowrap text-center">
						@if($data['credit'])
						{{ \App\Utils\Util::format_currency($data['credit'], $business->currency) }}
						@endif
						@if($business->second_currency_id > 0 && $data['credit_currency2'])
						{{ ($data['credit'] > 0) ? '/' : '' }}
						{{ \App\Utils\Util::format_currency($data['credit_currency2'], $business->secondCurrency) }}
						@endif
					</td>
					<td class="ws-nowrap text-center">
						@if(abs($data['balance']) > 0)
							{{ \App\Utils\Util::format_currency(abs($data['balance']), $business->currency) }}
							@if($data['balance'] < 0)
								@lang('lang_v1.dr')
							@else
								@lang('lang_v1.cr')
							@endif
						@endif

						@if($business->second_currency_id > 0 && abs($data['balance_currency2']) > 0)
							{{ abs($data['balance']) > 0 ? '/' : ''  }}
							{{ \App\Utils\Util::format_currency(abs($data['balance_currency2']), $business->secondCurrency) }}
							@if($data['balance_currency2'] > 0)
								@lang('lang_v1.dr')
							@else
								@lang('lang_v1.cr')
							@endif
						@endif
					</td>
					<td>
						{{$data['payment_method']}}
					</td>
					<td>
						<small>{{ $data['others'] }}</small>

						@if(!empty($is_admin) && !empty($data['transaction_id']) && $data['transaction_type'] == 'ledger_discount')
							<br>
							<button type="button" class="btn btn-xs btn-danger delete_ledger_discount" data-href="{{action([\App\Http\Controllers\LedgerDiscountController::class, 'destroy'], ['ledger_discount' => $data['transaction_id']])}}"><i class="fas fa-trash"></i></button>
							<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="{{action([\App\Http\Controllers\LedgerDiscountController::class, 'edit'], ['ledger_discount' => $data['transaction_id']])}}" data-container="#edit_ledger_discount_modal"><i class="fas fa-edit"></i></button>
						@endif
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	</div>
</div>