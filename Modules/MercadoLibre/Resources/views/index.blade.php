@extends('layouts.app')
@section('title', 'MercadoLibre')

@section('content')
@include('mercadolibre::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('mercadolibre::lang.mercadolibre')</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
        $is_superadmin = auth()->user()->can('superadmin');
    @endphp

    <div class="row">
        @if(!empty($alerts['connection_failed']))
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible d-flex align-items-center" style="margin-left:15px; margin-right: 15px;">
                <ul>
                    <li>{{$alerts['connection_failed']}}</li>
                </ul>
                <span class="ms-4">Connect To <a href="{{ $meli_auth_link }}">Mercado Libre</a></span>
            </div>
        </div>
        @endif
        <div class="col-sm-6">
            @if($is_superadmin || auth()->user()->can('mercadolibre.sync_products') )
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-cubes"></i>
                        <h3 class="box-title">@lang('mercadolibre::lang.sync_products'):</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-sm-6">
                            <div style="display: inline-flex; width: 100%;">
                                <button 
                                    type="button" 
                                    class="btn btn-warning btn-block sync_products" 
                                    data-sync-type="new"
                                    {{ $mercadolibre_connected ? '' : 'disabled' }}
                                > 
                                    <i class="fa fa-refresh"></i> 
                                    <span>@lang('mercadolibre::lang.sync_only_new')</span>
                                    <span class="ms-2">({{ $not_synced_product_count ?? 0 }})</span>
                                </button> 
                                &nbsp;@show_tooltip(__('mercadolibre::lang.sync_new_help'))
                            </div>
                            <span class="last_sync_new_products"></span>
                        </div>
                        <div class="col-sm-6">
                            <div style="display: inline-flex; width: 100%;">
                                <button type="button" 
                                    class="btn btn-primary btn-block sync_products" 
                                    data-sync-type="all"
                                    {{ $mercadolibre_connected ? '' : 'disabled' }}
                                > 
                                    <i class="fa fa-refresh"></i> 
                                    <span>@lang('mercadolibre::lang.sync_all')</span>
                                    <span class="ms-2">({{ $updated_product_count ?? 0}} )</span>
                                </button> 
                                &nbsp;@show_tooltip(__('mercadolibre::lang.sync_all_help'))
                            </div>
                            <span class="last_sync_all_products"></span>
                        </div>
                        <div class="col-sm-12">
                            <br>
                            <button type="button" 
                                class="btn btn-danger btn-xs" 
                                id="reset_products"
                                {{ $mercadolibre_connected ? '' : 'disabled' }}
                            > 
                                <i class="fa fa-undo"></i> 
                                <span>@lang('mercadolibre::lang.reset_synced_products')</span>
                            </button>
                        </div>
                    </div>
               </div>
           </div>
           @endif
           @if($is_superadmin || auth()->user()->can('mercadolibre.sync_orders') )
           <div class="col-sm-12">
               <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-cart-plus"></i>
                        <h3 class="box-title">@lang('mercadolibre::lang.sync_orders'):</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-sm-6">
                            <button type="button" 
                                class="btn btn-success btn-block" id="sync_orders"
                                {{ $mercadolibre_connected ? '' : 'disabled' }}
                            > 
                                <i class="fa fa-refresh"></i> 
                                <span>@lang('mercadolibre::lang.sync')</span>
                            </button>
                            <span class="last_sync_orders"></span>
                        </div>
                    </div>
               </div>
            </div>
            @endif
        </div>
    </div>
    
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function() {
        syncing_text = '<i class="fa fa-refresh fa-spin"></i> ' + "{{__('mercadolibre::lang.syncing')}}...";

        //Sync Products
        $('.sync_products').click( function(){
            var btn = $(this);
            var btn_html = btn.html();
            btn.html(syncing_text); 
            btn.attr('disabled', true);

            sync_products(btn, btn_html);
        });

        //Sync Orders
        $('#sync_orders').click( function(){
            $(window).bind('beforeunload', function(){
                return true;
            });
            var btn = $(this);
            var btn_html = btn.html(); 
            btn.html(syncing_text); 
            btn.attr('disabled', true);

            $.ajax({
                url: "{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'syncOrders'])}}",
                dataType: "json",
                timeout: 0,
                success: function(result){
                    if(result.success){
                        toastr.success(result.msg);
                        update_sync_date();
                    } else {
                        toastr.error(result.msg);
                    }
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });            
        });
    });

    function update_sync_date() {
        $.ajax({
            url: "{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'getSyncLog'])}}",
            dataType: "json",
            timeout: 0,
            success: function(data){
                if(data.new_products){
                    $('span.last_sync_new_products').html('<small>{{__("mercadolibre::lang.last_synced")}}: ' + data.new_products + '</small>');
                }
                if(data.all_products){
                    $('span.last_sync_all_products').html('<small>{{__("mercadolibre::lang.last_synced")}}: ' + data.all_products + '</small>');
                }
                if(data.orders){
                    $('span.last_sync_orders').html('<small>{{__("mercadolibre::lang.last_synced")}}: ' + data.orders + '</small>');
                }
                
            }
        });     
    }

    //Reset Synced Categories
    $(document).on('click', 'button#reset_categories', function(){
        var checkbox = document.createElement("div");
        checkbox.setAttribute('class', 'checkbox');
        checkbox.innerHTML = '<label><input type="checkbox" id="yes_reset_cat"> {{__("mercadolibre::lang.yes_reset")}}</label>';
        swal({
          title: LANG.sure,
          text: "{{__('mercadolibre::lang.confirm_reset_cat')}}",
          icon: "warning",
          content: checkbox,
          buttons: true,
          dangerMode: true,
        }).then((confirm) => {
            if(confirm) {
                if($('#yes_reset_cat').is(":checked")) {
                    $(window).bind('beforeunload', function(){
                        return true;
                    });
                    var btn = $(this);
                    btn.attr('disabled', true);
                    $.ajax({
                        url: "{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'resetCategories'])}}",
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    //Reset Synced products
    $(document).on('click', 'button#reset_products', function(){
        var checkbox = document.createElement("div");
        checkbox.setAttribute('class', 'checkbox');
        checkbox.innerHTML = '<label><input type="checkbox" id="yes_reset_product"> {{__("mercadolibre::lang.yes_reset")}}</label>';
        swal({
          title: LANG.sure,
          text: "{{__('mercadolibre::lang.confirm_reset_product')}}",
          icon: "warning",
          content: checkbox,
          buttons: true,
          dangerMode: true,
        }).then((confirm) => {
            if(confirm) {
                if($('#yes_reset_product').is(":checked")) {
                    $(window).bind('beforeunload', function(){
                        return true;
                    });
                    var btn = $(this);
                    btn.attr('disabled', true);
                    $.ajax({
                        url: "{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'resetProducts'])}}",
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    function sync_products(btn, btn_html, page=0) {
        var type = btn.data('sync-type');
        $.ajax({
            url: "{{action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'syncProducts'])}}?type=" + type + "&page=" + page,
            dataType: "json",
            timeout: 0,
            success: function(result){
                if(result.success){
                    if(result.errors?.length > 0) {
                        result.errors.forEach(function(error) {
                            toastr.error(error);
                        })
                    }

                    if (result.sync_count > 0) {
                        toastr.success(`${result.sync_count} products has been synchronized successfully.`);
                        page++;
                        sync_products(btn, btn_html, page)
                    } else {
                        window.location.reload();
                    }
                } else {
                    toastr.error(result.msg);
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            }
        });     
    }
</script>
@endsection
