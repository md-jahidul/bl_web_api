<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/v1'], function () {
    Route::get('menu','API\V1\MenuApiController@getMenu');
    Route::get('header-footer','API\V1\HeaderFooterMenuApiController@getFooterMenu');
    Route::get('home-page','API\V1\HomeDataDynamicApiController@getHomeData');
    Route::get('digital-services','API\V1\DigitalServiceController@getDigitalService');
    Route::get('partner-offers','API\V1\OfferApiController@index');
    Route::get('ssl','API\V1\SslCommerzController@ssl');
});

