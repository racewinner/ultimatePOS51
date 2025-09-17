<div class="well">
<ul class="text-danger">
	@foreach($log_details as $details)
		@if(is_object($details) && !empty($details->error_type))
		<li>
			@if($details->error_type == 'order_product_not_found')
				{!! __('mercadolibre::lang.product_not_found_error', ['product' => $details->product, 'order_number' => $details->order_number]) !!}
			@elseif($details->error_type == 'order_insuficient_product_qty')
				{!! __('mercadolibre::lang.qty_mismatch_error', ['msg' => $details->msg, 'order_number' => $details->order_number]) !!}
			@elseif($details->error_type == 'order_customer_empty')
				{!! __('mercadolibre::lang.order_customer_empty', ['order_number' => $details->order_number]) !!}
			@endif
		</li>
		@endif
	@endforeach
</ul>
</div>