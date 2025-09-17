<div class="meli-category">
    <input type="hidden" id="mercadolibre_category_id" name="mercadolibre_category_id"
        value="{{ $category->mercadolibre_category_id ?? '' }}">

    <div class="">
        <label class="m-0 me-2">Current Category:</label>
        <div class="d-flex align-items-center">
            <nav id="category_path" aria-label="breadcrumb">
                <ol class="breadcrumb m-0 px-0">
                </ol>
            </nav>
            <i id="mercadolibre-category-selected" class="fa fa-check-circle text-info hidden ms-2"></i>
        </div>
    </div>

    <div class="d-flex align-items-center mt-2" style="width: 500px;">
        <label class="m-0" style="width:150px;">Sub Categories: </label>
        {!! Form::select('meli_sub_categories', [], '', ['placeholder' => __('messages.please_select'), 'id' => 'meli_sub_categories', 'class' => 'form-control select2']) !!}
    </div>

    <p class="mt-4 comment">( @lang('lang_v1.select_leaf_category_ml') )</p>
</div>