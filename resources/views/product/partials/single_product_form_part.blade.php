@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

<div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
        <tr>
          <th>@lang('product.default_purchase_price')</th>
          <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
          <th>@lang('product.default_selling_price')</th>
          @if(empty($quick_add))
            <th>@lang('lang_v1.product_image')</th>
          @endif
        </tr>
        <tr>
          <td>
            <div class="col-sm-6">
              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}

              {!! Form::text('single_dpp', $default, ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']) !!}
            </div>

            <div class="col-sm-6">
              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
            
              {!! Form::text('single_dpp_inc_tax', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']) !!}
            </div>

            <div class="col-sm-6 mt-4 mb-4">
              <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                  </span>
                  <select name='currency_id' class='form-control select2' required style="width:100%;">
                    @foreach ($currencies as $currency)
                      <option value={{ $currency->id }} 
                          data-thousand-separator={{ empty($currency->thousand_separator) ? "NA" : $currency->thousand_separator }}
                          data-decimal-separator={{ empty($currency->decimal_separator) ? "NA" : $currency->decimal_separator}}
                          data-symbol={{ $currency->symbol }}
                      > 
                        {{ $currency->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mt-4" style="margin-bottom:50px;">
              <div class="form-group">
                {!! Form::label('exchange_rate', __('lang_v1.currency_exchange_rate') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                  </span>
                  {!! Form::number('nation_exchg_rate', $business->nation_exchg_rate, ['class' => 'form-control', 'placeholder' => __('lang_v1.currency_exchange_rate') ]) !!}
                </div>
              </div>
            </div>
          </td>
          <td>
            <br/>
            {!! Form::text('profit_percent', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent', 'required']) !!}
          </td>

          <td>
            <label><span class="dsp_label">@lang('product.exc_of_tax')</span></label>
            {!! Form::text('single_dsp', $default, ['class' => 'form-control input-sm dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp', 'required']) !!}

            {!! Form::text('single_dsp_inc_tax', $default, ['class' => 'form-control input-sm hide input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax', 'required']) !!}
          </td>
          @if(empty($quick_add))
          <td>
              <div class="form-group">
                {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
                {!! Form::file('variation_images[]', ['class' => 'variation_images', 
                    'accept' => 'image/*', 'multiple']) !!}
                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
              </div>
          </td>
          @endif
        </tr>
    </table>
</div>