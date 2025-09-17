<div class="col-md-3">
	<div class="form-group">
		@php
			$is_disabled = !empty($product->mercadolibre_disable_sync) ? true : false;
      if(empty($product) && !empty($duplicate_product->mercadolibre_disable_sync)){
        $is_disabled = true;
      }
		@endphp
      <br>
        <label>
        	<input type="hidden" name="mercadolibre_disable_sync" value="0">
          	{!! Form::checkbox('mercadolibre_disable_sync', 1, $is_disabled, ['class' => 'input-icheck']); !!} <strong>@lang('mercadolibre::lang.mercadolibre_disable_sync')</strong>
        </label>
        @show_tooltip(__('mercadolibre::lang.mercadolibre_disable_sync_help'))
  	</div>
</div>