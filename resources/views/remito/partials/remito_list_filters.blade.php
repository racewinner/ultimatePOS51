<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('remito_list_filter_location_id', __('purchase.business_location') . ':') !!}
        {!! Form::select('remito_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('remito_list_filter_customer_id', __('contact.customer') . ':') !!}
        {!! Form::select('remito_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('remito_list_filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('remito_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
    </div>
</div>
@if(!empty($sales_representative))
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('created_by', __('report.user') . ':') !!}
            {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
        </div>
    </div>
@endif