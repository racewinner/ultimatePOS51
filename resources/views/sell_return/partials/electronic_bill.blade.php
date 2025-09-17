<div class="electronic_bill_tab my-2" style="margin: 10px 0px;" data-type="card" >
	@if ($sell_return)
		@if ($sell_return->pymo_invoice)
			<i class="fa fa-download"></i>
			<a class="m-2" href="/uploads/invoices/{{$sell_return->business_id}}/{{$sell_return->pymo_invoice . '.pdf'}}" target="_blank">Download Document</a>
		@else
			<button href="#" id="generate_bill">@lang( 'lang_v1.generate_invoice' )</button>
		@endif
  @else
    <div class="checkbox">
      <label>
      {!! Form::checkbox('generate_bill_after_create', 1, false, ['class' => 'toggler', 'data-toggle_id' => 'parent_cat_div', 'id' => 'generate_bill_after_create']) !!}
      @lang('lang_v1.generate_invoice')
      </label>
    </div>
	@endif
</div>
