<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('location_id',  __('business.business_locations') . ':') !!} @show_tooltip(__('mercadolibre::lang.location_dropdown_help'))
                {!! Form::select('location_id', $locations, $default_settings['location_id'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="checkbox">
                <label>
                    <br/>
                    {!! Form::checkbox('enable_auto_sync', 1, !empty($default_settings['enable_auto_sync']), ['class' => 'input-icheck'] ); !!} @lang('mercadolibre::lang.enable_auto_sync')
                </label>
                @show_tooltip(__('mercadolibre::lang.auto_sync_tooltip'))
            </div>
        </div>
    </div>
</div>