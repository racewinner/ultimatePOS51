<div class="modal fade" tabindex="-1" role="dialog" id="modal_select_invoice_layout" style="z-index:1051 !important;">
    <div class="modal-dialog" role="document" style="width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('invoice.invoice_layout')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label("pdf_format", __('invoice.invoice_pdf_format')) !!}
                    {!! Form::select('pdf_format', $pdf_formats, 'ticket', ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="print-invoice" data-href=""></a>
                <button class="btn btn-primary" id="btn_confirm">@lang('lang_v1.confirm')</button>
            </div>
        </div>
    </div>
</div>