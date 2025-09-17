<!-- Modal -->
<div class="modal fade" id="addReceiverModal" tabindex="-1" role="dialog" aria-labelledby="addReceiverModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReceiverModalTitle">
                    @lang('notifications.add_receiver_modal')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 1.5rem; right: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    {!! Form::label('receiver_id', __('notifications.receiver') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('receiver_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'receiver_id']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>