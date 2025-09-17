<div class="meli-category">
    <input type="hidden" id="mercadolibre_category_id" name="mercadolibre_category_id" value="{{ $product->mercadolibre_category_id ?? '' }}">

    <div class="checkbox">
        <label>
        {!! Form::checkbox('mercadolibre_disable_sync', 1, $product->mercadolibre_disable_sync, ['class' => 'input-icheck']) !!} <strong>Disable Mercado Libre Sync</strong>
        </label>
    </div>

    <div class="d-flex align-items-center">
        <label class="m-0 me-2">Current Category:</label>
        <nav id="category_path" aria-label="breadcrumb">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item cursor-pointer"><a data-cat-id='top'>Top Category</a></li>
            @if(!empty($product->mercadolibre_category))
                @foreach($product->mercadolibre_category->path_from_root as $cat)
                    <li class="breadcrumb-item cursor-pointer {{ $product->mercadolibre_category_id == $cat->id ? 'active' : '' }}"><a data-cat-id='{{ $cat->id }}'>{{ $cat->name }}</a></li>    
                @endforeach
            @endif
        </ol>
        </nav>
        <i id="mercadolibre-category-selected" class="fa fa-check-circle text-info hidden"></i>
    </div>

    <div id="mercardolibre-product-detail-setting" style="margin-top: 20px;">
        <h5>Required Attributes:</h5>
        <div class="row mt-2">
        <div class="col-sm-3">
            <div class="form-group">
            {!! Form::label('mercadolibre_item_condition', __('lang_v1.mercadolibre_item_condition') . ':') !!}<br>
            {!! Form::select(
                'mercadolibre_item_condition', 
                $product->mercadolibre_category->item_conditions, 
                $product->mercadolibre_details->mercadolibre_item_condition,
                ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); 
            !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
            {!! Form::label('mercadolibre_listing_type', __('lang_v1.mercadolibre_listing_type') . ':') !!}<br>
            {!! Form::select('mercadolibre_listing_type', 
                $mercadolibre_listing_types, 
                !empty($product->mercadolibre_details->mercadolibre_listing_type) ? $product->mercadolibre_details->mercadolibre_listing_type : '', 
                ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); 
            !!}
            </div>
        </div>
        </div>

        <div id="mercardolibre-required-attributes" class="row mt-2" style="flex-wrap:wrap">
        </div>
    </div>
</div>