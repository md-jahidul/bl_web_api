<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'], function () {
    Route::get('menu','API\V1\MenuApiController@getMenu');
    Route::get('header-footer','API\V1\MenuController@getHeaderFooterMenus');
    Route::get('home-page','API\V1\HomePageController@getHomePageData');
    // Route::get('digital-services','API\V1\DigitalServiceController@getDigitalService');
    Route::get('partner-offers','API\V1\OfferCategoryController@index');
    Route::get('offers/{type}','API\V1\OfferCategoryController@offers');
    Route::get('offers-categories','API\V1\OfferCategoryController@offerCategories');
    Route::get('product-details/{type}/{id}','API\V1\OfferCategoryController@productDetails');

    Route::get('ssl','API\V1\SslCommerzController@ssl');
    Route::get('ssl-api','API\V1\SslCommerzController@sslApi');
    Route::post('success','API\V1\SslCommerzController@success');
    Route::post('failure','API\V1\SslCommerzController@failure');
    Route::post('cancel','API\V1\SslCommerzController@cancel');

    Route::get('ebl-pay','API\V1\EblPaymentApiController@postData');
    Route::get('ebl-pay/complete/{order_id}','API\V1\EblPaymentApiController@complete');
    Route::get('ebl-pay/cancel','API\V1\EblPaymentApiController@cancel');

    Route::get('macro','API\V1\HomeDataDynamicApiController@macro');
});
