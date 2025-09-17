<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4>@lang('mercadolibre::lang.order_created')</h4>
        </div>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('mercadolibre_wh_oc_secret',  __('mercadolibre::lang.webhook_secret') . ':') !!}
            	{!! Form::text('mercadolibre_wh_oc_secret', !empty($business->mercadolibre_wh_oc_secret) ? $business->mercadolibre_wh_oc_secret : null, ['class' => 'form-control','placeholder' => __('mercadolibre::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('mercadolibre::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreWebhookController::class, 'orderCreated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('mercadolibre::lang.order_updated')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('mercadolibre_wh_ou_secret',  __('mercadolibre::lang.webhook_secret') . ':') !!}
                {!! Form::text('mercadolibre_wh_ou_secret', !empty($business->mercadolibre_wh_oc_secret) ? $business->mercadolibre_wh_ou_secret : null, ['class' => 'form-control','placeholder' => __('mercadolibre::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('mercadolibre::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreWebhookController::class, 'orderUpdated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('mercadolibre::lang.order_deleted')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('mercadolibre_wh_od_secret',  __('mercadolibre::lang.webhook_secret') . ':') !!}
                {!! Form::text('mercadolibre_wh_od_secret', !empty($business->mercadolibre_wh_oc_secret) ? $business->mercadolibre_wh_od_secret : null, ['class' => 'form-control','placeholder' => __('mercadolibre::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('mercadolibre::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreWebhookController::class, 'orderDeleted'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('mercadolibre::lang.order_restored')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('mercadolibre_wh_or_secret',  __('mercadolibre::lang.webhook_secret') . ':') !!}
                {!! Form::text('mercadolibre_wh_or_secret', !empty($business->mercadolibre_wh_oc_secret) ? $business->mercadolibre_wh_or_secret : null, ['class' => 'form-control','placeholder' => __('mercadolibre::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('mercadolibre::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreWebhookController::class, 'orderRestored'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

    </div>
</div>