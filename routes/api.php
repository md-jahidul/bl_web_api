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


    Route::get('product-details/{id}','API\V1\ProductDetailsController@productDetails');


    // QUICK LAUNCH  ====================================
    Route::get('quick-launch/button', 'API\V1\QuickLaunchController@getQuickLaunchItems');

    //AMAR OFFER ========================================
    Route::get('amar-offer', 'API\V1\AmarOfferController@getAmarOfferList');
    Route::post('amar-offer/buy', 'API\V1\AmarOfferController@buyAmarOffer');

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
    Route::get('product/loan/{loanType}', 'API\V1\ProductController@customerLoanProducts');

    //Loyalty or Priyojon section
    Route::get('priyojon/status', 'API\V1\LoyaltyController@priyojonStatus');
    Route::get('priyojon/redeem/options', 'API\V1\LoyaltyController@redeemOptions');


    Route::get('popular-search/', 'API\V1\SearchController@getPopularSearch');
    Route::get('search-suggestion/{keyword}', 'API\V1\SearchController@getSearchSuggestion');
    Route::get('search/{keyword}', 'API\V1\SearchController@getSearchData');

    //Easy payment card
     Route::get('easy-payment-cards/{division?}/{area?}', 'API\V1\EasyPaymentCardController@cardList');
     Route::get('easy-payment-area-list/{division}', 'API\V1\EasyPaymentCardController@getAreaList');

    //Device offer
     Route::get('device-offers/{brand?}/{model?}', 'API\V1\DeviceOfferController@offerList');


     //Business Module APIs
     Route::get('business-home-data', 'API\V1\BusinessController@index');
     Route::get('business-categories', 'API\V1\BusinessController@getCategories');
     Route::get('business-packages', 'API\V1\BusinessController@packages');
     Route::get('business-packages-details/{packageId}', 'API\V1\BusinessController@packageById');
     Route::get('business-internet-package', 'API\V1\BusinessController@internet');
     Route::get('business-internet-details/{internetId}', 'API\V1\BusinessController@internetDetails');
     Route::get('business-internet-like/{internetId}', 'API\V1\BusinessController@internetLike');
     Route::get('business-enterprise-package/{type}', 'API\V1\BusinessController@enterpriseSolusion');

     Route::get('business-enterprise-package-details/{serviceId}', 'API\V1\BusinessController@enterpriseProductDetails');
     
     
     //roaming Module APIs
     Route::get('roaming-categories', 'API\V1\RoamingController@getCategories');

     // eCarrer api
     Route::get('ecarrer/banner-contact', 'API\V1\EcareerController@topBannerContact');
     Route::get('ecarrer/life-at-bl', 'API\V1\EcareerController@lifeAtBanglalink');

     Route::get('ecarrer/programs', 'API\V1\EcareerController@getEcarrerPrograms');
     Route::get('ecarrer/vacancy', 'API\V1\EcareerController@getEcarrerVacancy');

     // eCarrer Application form api  =========================================================
    Route::get('ecarrer/university', 'API\V1\EcareerController@ecarrerUniversity');
    Route::post('ecarrer/application-form', 'API\V1\EcareerController@ecarrerApplicationForm');


    // AboutUsBanglalink
    Route::get('about-us-banglalink', 'API\V1\AboutUsController@getAboutBanglalink');
    Route::get('about-us-management', 'API\V1\AboutUsController@getAboutManagement');
    Route::get('about-us-eCareer', 'API\V1\AboutUsController@getEcareersInfo');

    // App And Service
    Route::get('app-service', 'API\V1\AppServiceController@appServiceAllComponent');
    Route::get('app-service/package-list/{provider}', 'API\V1\AppServiceController@packageList');
    Route::get('app-service/like/{productId}', 'API\V1\AppServiceController@appServiceLike');
    Route::post('app-service/bookmark/save-or-remove', 'API\V1\AppServiceController@bookmarkSaveOrDelete');

    // VAS Apis
    Route::post('vas/subscription', 'API\V1\VasApiController@subscription');
    Route::post('vas/checkSubStatus', 'API\V1\VasApiController@checkSubStatus');
    Route::post('vas/cancel-subscription', 'API\V1\VasApiController@cancelSubscription');

    Route::get('vas/{providerUrl}/content-list', 'API\V1\VasApiController@contentList');
    Route::get('vas/{providerUrl}/content-detail/{contentId}', 'API\V1\VasApiController@contentDetail');

    # Sales and Service search results
    Route::post('sales-service/search-results', 'API\V1\SalesServiceController@salesServiceSearchResutls');

    Route::get('sales-service/districts', 'API\V1\SalesServiceController@salesServiceGetDistricts');
    Route::post('sales-service/thana-by-district', 'API\V1\SalesServiceController@salesServiceThanaByDistricts');

    // App and service get details page with product id
    Route::get('app-service/details/{id}', 'API\V1\AppServiceDetailsController@appServiceDetailsComponent');

    # Frontend route for seo tab
    Route::get('frontend-route', 'API\V1\HomePageController@frontendDynamicRoute');


    //Lead Request
    Route::post('lead-request', 'API\V1\LeadManagementController@leadRequestData');

    //District Thana
    Route::get('district', 'API\V1\DistrictThanaController@district');
    Route::get('thana/{districtId}', 'API\V1\DistrictThanaController@thana');

});
