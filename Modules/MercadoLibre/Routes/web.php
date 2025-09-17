<?php
Route::post('/webhook/mercadolibre/order-created/{business_id}','MercadoLibreWebhookController@orderCreated');
Route::post('/webhook/mercadolibre/order-updated/{business_id}','MercadoLibreWebhookController@orderUpdated');
Route::post('/webhook/mercadolibre/order-deleted/{business_id}','MercadoLibreWebhookController@orderDeleted');
Route::post('/webhook/mercadolibre/order-restored/{business_id}','MercadoLibreWebhookController@orderRestored');

Route::middleware('web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu')->prefix('mercadolibre')->group(function () {
    Route::get('/', 'MercadoLibreController@index');
    Route::get('/auth', 'MercadoLibreController@auth');
    Route::get('/test', 'MercadoLibreController@test');
    Route::get('/disconnect', 'MercadoLibreController@disconnect');
    Route::get('/validate_token', 'MercadoLibreController@validateToken');
    Route::get('/create_testuser', 'MercadoLibreController@create_testuser');
    Route::get('/sites/{site_id}/listing_type', 'MercadoLibreController@getListingTypes');
    Route::get('/products', 'MercadoLibreController@getProducts');
    Route::get('/categories/{cat_id}', 'MercadoLibreController@getCategory');
    Route::get('/categories/{cat_id}/detail', 'MercadoLibreController@getCategoryDetail');
    Route::get('/categories/{cat_id}/attributes', 'MercadoLibreController@getCategoryAttributes');

    Route::get('/view-sync-log', 'MercadoLibreController@viewSyncLog');
    Route::get('/api-settings', 'MercadoLibreController@apiSettings');
    Route::post('/update-api-settings', 'MercadoLibreController@updateSettings');
    Route::post('/map-taxrates', 'MercadoLibreController@mapTaxRates');
    Route::get('/sync-categories', 'MercadoLibreController@syncCategories'); 
    Route::get('/sync-products', 'MercadoLibreController@syncProducts'); 
    Route::get('/sync-orders', 'MercadoLibreController@syncOrders'); 
    Route::get('/sync-log', 'MercadoLibreController@getSyncLog'); 
    Route::get('/get-log-details/{id}', 'MercadoLibreController@getLogDetails'); 
    Route::get('/reset-categories', 'MercadoLibreController@resetCategories'); 
    Route::get('/reset-products', 'MercadoLibreController@resetProducts'); 
});
