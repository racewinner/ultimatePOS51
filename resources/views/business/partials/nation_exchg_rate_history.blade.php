<div class="pos-tab-content">
    <div class="d-flex w-100 justify-content-end">
        <div class="form-group" style="width: 300px;">
            {!! Form::label('nerh_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('nerh_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'nerh_date_range', 'readonly']) !!}
        </div>
    </div>
    <div>
        <iframe id="nerh_iframe" class="w-100" style="height: 450px; border: 0;">
        </iframe>
    </div>
    <div>
        <table class="table table-condensed table-bordered table-th-green text-center table-striped" id="nerh_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('lang_v1.exchg_rate')</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>