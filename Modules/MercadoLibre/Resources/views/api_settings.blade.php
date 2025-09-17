@extends('layouts.app')
@section('title', __('mercadolibre::lang.api_settings'))

@section('content')
@include('mercadolibre::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('mercadolibre::lang.api_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\MercadoLibre\Http\Controllers\MercadoLibreController@updateSettings', 'method' => 'post']) !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('mercadolibre::lang.instructions')</a>
                        <a href="#" class="list-group-item text-center">@lang('mercadolibre::lang.api_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('mercadolibre::lang.product_sync_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('mercadolibre::lang.order_sync_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('mercadolibre::lang.webhook_settings')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('mercadolibre::partials.api_instructions')
                    @include('mercadolibre::partials.api_settings')
                    @include('mercadolibre::partials.product_sync_settings')
                    @include('mercadolibre::partials.order_sync_settings')
                    @include('mercadolibre::partials.webhook_settings')
                </div>
            </div>

            <div class="col-xs-12">
                <p class="help-block"><i>{!! __('mercadolibre::lang.version_info', ['version' => $module_version]) !!}</i></p>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit('update', ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        $('.create_quantity').on('ifChecked', function(event){
            $('.create_stock_settings').each( function(){
                $(this).addClass('hide');
            });
        });
        $('.create_quantity').on('ifUnchecked', function(event){
            $('.create_stock_settings').each( function(){
                $(this).removeClass('hide');
            });
        });
        $('.update_quantity').on('ifChecked', function(event){
            $('.update_stock_settings').each( function(){
                $(this).addClass('hide');
            });
        });
        $('.update_quantity').on('ifUnchecked', function(event){
            $('.update_stock_settings').each( function(){
                $(this).removeClass('hide');
            });
        });
    });
</script>
@endsection