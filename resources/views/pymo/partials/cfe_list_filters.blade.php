<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('cfeType', 'CFE Type' . ':') !!}
        {!! Form::select('cfeType', $cfeTypes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('cfeStatus', 'CFE Status' . ':') !!}
        {!! Form::select('cfeStatus', $cfeStatus, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('cfe_list_filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('cfe_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
    </div>
</div>