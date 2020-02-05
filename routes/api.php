<?php
//header('Access-Control-Allow-Origin: https://assetlite.banglalink.net');
//header('Access-Control-Allow-Origin: http://172.16.8.160:9443');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::get('partner-offers','API\V1\PartnerOfferController@index');
    Route::get('offers/{type}','API\V1\ProductController@simPackageOffers');
    Route::get('offers-categories','API\V1\PartnerOfferController@offerCategories');
    Route::get('product-details/{type}/{id}','API\V1\ProductController@productDetails');

    // QUICK LAUNCH  ====================================
    Route::get('quick-launch/button', 'API\V1\QuickLaunchController@getQuickLaunchItems');

    //AMAR OFFER ========================================
    Route::get('amar-offer', 'API\V1\AmarOfferController@getAmarOfferList');
    Route::get('amar-offer-details/{type}', 'API\V1\AmarOfferController@getAmarOfferDetails');

    Route::get('product-like/{id}','API\V1\ProductController@productLike');
    Route::post('product/bookmark','API\V1\ProductController@bookmarkProduct');
    Route::get('customer/product/bookmark','API\V1\ProductController@getCustomerBookmarkProducts');
    Route::get('customer/products','API\V1\ProductController@customerSavedBookmarkProduct');


    Route::get('recharge-offers/view/{amount}', 'API\V1\ProductController@rechargeOfferByAmount');
    Route::get('recharge-offers', 'API\V1\ProductController@rechargeOffers');

    Route::get('priyojon-header','API\V1\PriyojonController@priyojonHeader');
    Route::get('priyojon-offers','API\V1\PriyojonController@priyojonOffers');

    Route::get('about-page/{slug}','API\V1\PriyojonController@getAboutPage');

    Route::get('offer-details/{id}','API\V1\PartnerOfferController@offerDetails');

    Route::post('ssl','API\V1\SslCommerzController@ssl');
    Route::get('ssl-api','API\V1\SslCommerzController@sslApi');
    Route::get('ssl/request/details','API\V1\SslCommerzController@getRequestDetails');
    Route::post('success','API\V1\SslCommerzController@success');
    Route::post('failure','API\V1\SslCommerzController@failure');
    Route::post('cancel','API\V1\SslCommerzController@cancel');

    Route::get('ebl-pay','API\V1\EblPaymentApiController@postData');
    Route::get('ebl-pay/complete/{order_id}','API\V1\EblPaymentApiController@complete');
    Route::get('ebl-pay/cancel','API\V1\EblPaymentApiController@cancel');

    Route::get('macro','API\V1\HomePageController@macro');

    Route::get('user/profile/view','API\V1\UserProfileController@view');
    Route::post('user/profile/update','API\V1\UserProfileController@update');
    Route::post('user/profile/image/update','API\V1\UserProfileController@updateProfileImage');
    Route::get('user/profile/image/remove','API\V1\UserProfileController@removeProfileImage');
    Route::get('user/number/validation/{mobile}','API\V1\AuthenticationController@numberValidation');
    Route::post('user/otp-login/request','API\V1\AuthenticationController@requestOtpLogin');
    Route::post('user/otp-login/perform','API\V1\AuthenticationController@otpLogin');

    // Refresh token
    Route::post('refresh', 'API\V1\AuthenticationController@getRefreshToken');

    Route::post('product/purchase', 'API\V1\ProductController@purchase');
    Route::get('product/list/{customerId}', 'API\V1\ProductController@getProducts');
    Route::get('product/loan', 'API\V1\ProductController@customerLoanProducts');

    //Loyalty or Priyojon section
    Route::get('priyojon/status', 'API\V1\LoyaltyController@priyojonStatus');
    Route::get('priyojon/redeem/options', 'API\V1\LoyaltyController@redeemOptions');


    Route::get('search/{keyWord}', 'API\V1\SearchController@getSearchResult');
    
    //Easy payment card
     Route::get('easy-payment-cards', 'API\V1\EasyPaymentCardController@cardList');
     Route::get('easy-payment-area-list', 'API\V1\EasyPaymentCardController@getAreaList');
   



});
