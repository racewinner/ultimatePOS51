<div class="modal fade" tabindex="-1" role="dialog" id="modal_pin_confirm">
    <div class="modal-dialog" role="document" style="width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.confirm_pin')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label("input_pin" , __('lang_v1.input_pin')) !!}
                    <input type="password" name="input_pin" id="input_pin" class="form-control" placeholder="PIN" autofocus>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm_pin">@lang('lang_v1.check')</button>
            </div>
        </div>
    </div>
</div>