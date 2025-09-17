@extends('layouts.app')

@php
    $title = __('lang_v1.remito');
@endphp

@section('title', $title)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{$title}}</h1>
</section>
<!-- Main content -->
<section class="content no-print">

<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
@if(session('business.enable_rp') == 1)
    <input type="hidden" id="reward_point_enabled">
@endif

@if(count($business_locations) > 0)
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-map-marker"></i>
				</span>
			{!! Form::select('select_location_id', $business_locations, $transaction->location_id, ['class' => 'form-control input-sm',
			    'id' => 'select_location_id', 'required', 'autofocus'], $bl_attributes) !!}
				<span class="input-group-addon">
					@show_tooltip(__('tooltip.sale_location'))
				</span> 
			</div>
		</div>
	</div>
</div>
@endif

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$common_settings = session()->get('business.common_settings');
@endphp
    <input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">

	{!! Form::open(['url' => action([\App\Http\Controllers\RemitoController::class, 'update'], [$transaction->id]), 'method' => 'put', 'id' => 'edit_remito_form', 'files' => true ]) !!}
	<input type='hidden' name='transaction_id' value='{{$transaction->id}}' />
	<div class="row">
		<div class="col-md-12 col-sm-12">
			@component('components.widget', ['class' => 'box-solid'])
			{!! Form::hidden('location_id', $transaction->location_id, ['id' => 'location_id', 'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser', 'data-default_payment_accounts' => $transaction->location->default_payment_accounts]) !!}
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
							<input type="hidden" id="default_customer_id" value="{{ $transaction->contact->id }}" >
							<input type="hidden" id="default_customer_name" value="{{ $transaction->contact->name }}" >
							<input type="hidden" id="default_customer_balance" value="{{ $transaction->contact->balance }}" >
							{!! Form::select('contact_id', 
								[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required']) !!}
						</div>
					</div>
					<small>
                        <strong>
                            @lang('lang_v1.billing_address'):
                        </strong>
                        <div id="billing_address_div">
                            {!! $transaction->contact->contact_address !!}
                        </div>
                        <br>
                        <strong>
                            @lang('lang_v1.shipping_address'):
                        </strong>
                        <div id="shipping_address_div">
                            {{ $transaction->contact->supplier_business_name ?? ''}},<br>
                            {{ $transaction->contact->name ?? ''}},<br>
                            {{ $transaction->contact->shipping_address ?? ''}}
                        </div>
					</small>
				</div>

                <div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime($transaction->transaction_date), ['class' => 'form-control', 'readonly', 'required', 'id' => 'transaction_date']) !!}
						</div>
					</div>
				</div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('invoice_no', __('sale.invoice_no') . ':') !!}
                        {!! Form::text('invoice_no', $transaction->invoice_no, ['class' => 'form-control', 'placeholder' => __('sale.invoice_no')]) !!}
                        <p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
                    </div>
                </div>

		        <div class="col-sm-3">
	                <div class="form-group">
	                    {!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
	                    {!! Form::file('sell_document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]) !!}
	                    <p class="help-block">
	                    	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
	                    	@includeIf('components.document_help_text')
	                    </p>
	                </div>
	            </div>
			@endcomponent

			@component('components.widget', ['class' => 'box-solid'])
				<div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white " data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
							</div>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
							'autofocus' => is_null($default_location)? false : true,
							]) !!}
						</div>
					</div>
				</div>

				<div class="row col-sm-12 remito_product_div" style="min-height: 0">
					<!-- Keeps count of product rows -->
					<input type="hidden" id="product_row_count" value="0">
					<div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped table-responsive" id="remito_edit_table">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        @lang('sale.product')
                                    </th>
                                    <th class="text-center">
                                        @lang('sale.qty')
                                    </th>
                                    <th class="@if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
                                        @lang('sale.unit_price')
                                    </th>
                                    <th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody>
								@foreach($remito_details as $remito_line)
									@include('remito.partials.product_row', 
										['product' => $remito_line, 
										'row_count' => $loop->index, 
										'sub_units' => !empty($sell_line->unit_details) ? $sell_line->unit_details : [],
										'action' => 'edit'
									])
								@endforeach
							</tbody>
                        </table>
					</div>
				</div>
			@endcomponent
		</div>
	</div>

	@component('components.widget', ['class' => 'box-solid', 'title' => ''])
		{!! Form::label('additional_notes', __('lang_v1.remito_note') . ':') !!}
		{!! Form::textarea('additional_notes', $transaction->additional_notes, ['class' => 'form-control', 'rows' => 3, 'maxlength' => 300]) !!}
	@endcomponent

	{!! Form::close() !!}

	@component('components.widget', ['class' => 'box-solid', 'title' => __('lang_v1.electronic_bill')])
		@include('remito.partials.electronic_bill')
	@endcomponent
	
	<div class="row">
		{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']) !!}
		<div class="col-sm-12 text-center">
			<button type="button" id="submit-remito" class="btn btn-primary btn-big">@lang('messages.save')</button>
			<button type="button" id="save-and-print" class="btn btn-success btn-big">@lang('lang_v1.save_and_print')</button>
		</div>
	</div>

</section>

@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
	<script src="{{ asset('js/remito.js?v=' . $asset_v) }}"></script>
	@yield('bill_javascript')
    <script type="text/javascript">
    	$(document).ready( function() {
    		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

			$('#is_export').on('change', function () {
	            if ($(this).is(':checked')) {
	                $('div.export_div').show();
	            } else {
	                $('div.export_div').hide();
	            }
	        });
    	});
    </script>
@endsection
