@if(empty($only) || in_array('filter_location_id', $only))
    <input type='hidden' id='trans_type' value="{{request()->trans_type}}" />
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('filter_location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('filter_location_id', $business_locations, request()->location_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
        </div>
    </div>
@endif
@if(empty($only) || in_array('filter_customer_id', $only))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('filter_customer_id', __('contact.customer') . ':') !!}
            {!! Form::select('filter_customer_id', $customers, request()->customer_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
        </div>
    </div>
@endif
@if(empty($only) || in_array('filter_date_range', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('filter_date_range', 
            ($formattedStartDate && $formattedEndDate ? $formattedStartDate . '~' . $formattedEndDate : null), 
            ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
    </div>
</div>
@endif
@if((empty($only) || in_array('created_by', $only)) && !empty($payments_representative))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('created_by', __('report.user') . ':') !!}
            {!! Form::select('created_by', $payments_representative, request()->created_by, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
        </div>
    </div>
@endif