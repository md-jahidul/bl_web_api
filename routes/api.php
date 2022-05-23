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

Route::group(['prefix' => '/v1', 'middleware' => ['audit.log']], function () {
    Route::get('menu', 'API\V1\MenuApiController@getMenu');
    Route::get('header-footer', 'API\V1\MenuController@getHeaderFooterMenus');
    Route::get('home-page', 'API\V1\HomePageController@getHomePageData');
    // Route::get('digital-services','API\V1\DigitalServiceController@getDigitalService');
//    Route::get('partner-offers', 'API\V1\PartnerOfferController@index');

    Route::get('offers/{type}/{offer_type?}', 'API\V1\ProductController@simPackageOffers');

    // Offers Categories
    Route::get('offers-categories', 'API\V1\OfferCategoryController@offerCategories');

    Route::get('product-details/{slug}', 'API\V1\ProductController@productDetails');

    Route::get('packages/related-products/{type}', 'API\V1\OfferCategoryController@getPackageRelatedProduct');


    Route::get('product-other-details/{slug}', 'API\V1\ProductDetailsController@productDetails');


    // QUICK LAUNCH  ====================================
    Route::get('quick-launch/button', 'API\V1\QuickLaunchController@getQuickLaunchItems');

    //AMAR OFFER ========================================
    Route::get('amar-offer', 'API\V1\AmarOfferController@getAmarOfferList');
    Route::post('amar-offer/buy', 'API\V1\AmarOfferController@buyAmarOffer');
    Route::get('product-like/{id}', 'API\V1\ProductController@productLike');
    Route::get('customer/products', 'API\V1\ProductController@customerSavedBookmarkProduct');

    //Bondho SIM OFFER ========================================
    Route::get('check/bondho-sim-offer/{mobile}', 'API\V1\BondhoSimOfferController@getBondhoSimOfferCheck');

    //Bookmark =======================================
    Route::post('product/bookmark/save-remove', 'API\V1\ProductController@bookmarkProductSaveRemove');
//    Route::get('customer/bookmark/app-and-service','API\V1\ProductController@getCustomerBookmarkProducts');
    Route::get('bookmark/app-and-service/', 'API\V1\ProductBookmarkController@getBookmarkAppService');
    Route::get('bookmark/business/', 'API\V1\ProductBookmarkController@getBookmarkBusiness');
    Route::get('bookmark/offers/', 'API\V1\ProductBookmarkController@getBookmarkOffers');


    Route::get('recharge-offers/view/{amount}', 'API\V1\ProductController@rechargeOfferByAmount');
    Route::get('recharge-offers', 'API\V1\ProductController@rechargeOffers');


    Route::post('ssl', 'API\V1\SslCommerzController@ssl');
    Route::get('ssl-api', 'API\V1\SslCommerzController@sslApi');
    Route::get('ssl/request/details', 'API\V1\SslCommerzController@getRequestDetails');
    Route::post('success', 'API\V1\SslCommerzController@success');
    Route::post('failure', 'API\V1\SslCommerzController@failure');
    Route::post('cancel', 'API\V1\SslCommerzController@cancel');

    Route::get('ebl-pay', 'API\V1\EblPaymentApiController@postData');
    Route::get('ebl-pay/complete/{order_id}', 'API\V1\EblPaymentApiController@complete');
    Route::get('ebl-pay/cancel', 'API\V1\EblPaymentApiController@cancel');

    Route::get('macro', 'API\V1\HomePageController@macro');

    Route::get('user/profile/view', 'API\V1\UserProfileController@view');
    Route::post('user/profile/update', 'API\V1\UserProfileController@update');
    Route::post('user/profile/image/update', 'API\V1\UserProfileController@updateProfileImage');
    Route::get('user/profile/image/remove', 'API\V1\UserProfileController@removeProfileImage');
    Route::get('user/number/validation/{mobile}', 'API\V1\AuthenticationController@numberValidation')->middleware('client.secret.token');
    Route::post('user/otp-login/request', 'API\V1\AuthenticationController@requestOtpLogin')->middleware('client.secret.token');
    Route::post('user/otp-login/perform', 'API\V1\AuthenticationController@otpLogin');

    // Get JWT token with credential
    Route::post('password-login', 'API\V1\AuthenticationController@passwordLogin');

    // Get JWT token with Refresh token
    Route::post('refresh', 'API\V1\AuthenticationController@getRefreshToken');

    // Balance
    Route::get('current-balance', 'API\V1\CurrentBalanceController@getCurrentBalance');
    Route::get('balance/summary', 'API\V1\CurrentBalanceController@getBalanceSummary');

    // Product Purchase
    Route::get('product/eligibility-check/{mobile}/{productCode}', 'API\V1\ProductController@eligibleCheck');
    Route::post('product/purchase', 'API\V1\ProductController@purchase');
    Route::get('product/list/{customerId}', 'API\V1\ProductController@getProducts');
    Route::get('product/loan/{loanType}/{msisdn}', 'API\V1\ProductController@customerLoanProducts');

    //Loyalty or Priyojon section
        // Bl API Hub part
//    Route::get('priyojon/redeem/options', 'API\V1\LoyaltyController@redeemOptions');
    Route::get('partner-offers', 'API\V1\LoyaltyController@partnerCatWithOffers');
    Route::get('loyalty/redeem/options', 'API\V1\LoyaltyController@redeemOptions');
    Route::get('loyalty/redeem-offer-purchase/{offerId}', 'API\V1\LoyaltyController@redeemOfferPurchase');
    Route::get('priyojon/status', 'API\V1\LoyaltyController@priyojonStatus');
    Route::get('partner-offers/like/{offerID}', 'API\V1\LoyaltyController@partnerOfferLike');

    // CMS part
    Route::get('partner-offers/campaign', 'API\V1\PriyojonController@partnerCampaignOffers');
    Route::get('priyojon-header', 'API\V1\PriyojonController@priyojonHeader');
    Route::get('priyojon-offers', 'API\V1\PriyojonController@priyojonOffers');
    Route::get('priyojon-discount-offers/{page}', 'API\V1\PriyojonController@discountOffers');
    Route::get('priyojon-offers-like/{id}', 'API\V1\PriyojonController@offerLike');
    Route::get('about-page/{slug}', 'API\V1\PriyojonController@getAboutPage');
    Route::get('offer-details/{id}', 'API\V1\PartnerOfferController@offerDetails');

    Route::get('priyojon-about-page/banner/{slug}', 'API\V1\PriyojonController@aboutBannerImage');

    //Web Site Search
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
    Route::get('business-packages-details/{packageSlug}', 'API\V1\BusinessController@packageBySlug');
    Route::get('business-internet-package', 'API\V1\BusinessController@internet');
    Route::get('business-internet-details/{internetSlug}', 'API\V1\BusinessController@internetDetails');
    Route::get('business-internet-like/{internetId}', 'API\V1\BusinessController@internetLike');
    Route::get('business-enterprise-package/{type}', 'API\V1\BusinessController@enterpriseSolusion');

    Route::get('business-enterprise-package-details/{serviceSlug}', 'API\V1\BusinessController@enterpriseProductDetails');


    //roaming Module APIs
    Route::get('roaming-categories', 'API\V1\RoamingController@getCategories');
    Route::get('roaming-country-list', 'API\V1\RoamingController@getCountries');
    Route::get('roaming-operator-list/{countryEn}', 'API\V1\RoamingController@getOperators');
    Route::get('roaming-page/{pageSlug}', 'API\V1\RoamingController@roamingGeneralPage');
    Route::get('roaming-offers', 'API\V1\RoamingController@offerPage');
    Route::get('roaming-offers-details/{offerSlug}', 'API\V1\RoamingController@otherOfferDetails');
    Route::get('roaming-rates-and-bundle/{country}/{operator}', 'API\V1\RoamingController@ratesAndBundle');
    Route::get('roaming-bundle-like/{bundleId}', 'API\V1\RoamingController@bundleLike');
    Route::get('roaming-other-offer-like/{offerId}', 'API\V1\RoamingController@otherOfferLike');
    Route::get('roaming-rates', 'API\V1\RoamingController@roamingRates');
    Route::get('roaming-info-tips', 'API\V1\RoamingController@infoTips');
    Route::get('roaming-info-tips-details/{infoSlug}', 'API\V1\RoamingController@infoTipsDetails');

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

    # Sales and Service
    Route::get('sales-service-locations', 'API\V1\SalesServiceController@getNearestStoreLocations');
    Route::post('sales-service/search-results', 'API\V1\SalesServiceController@salesServiceSearchResutls');
    Route::get('sales-service/districts', 'API\V1\SalesServiceController@salesServiceGetDistricts');
    Route::post('sales-service/thana-by-district', 'API\V1\SalesServiceController@salesServiceThanaByDistricts');

    // App and service get details page with product id
    Route::get('app-service/details/{slug}', 'API\V1\AppServiceDetailsController@appServiceDetailsComponent');

    # Frontend route for seo tab
    Route::get('frontend-route', 'API\V1\HomePageController@frontendDynamicRoute');


    //Lead Request
    Route::post('lead-request', 'API\V1\LeadManagementController@leadRequestData');

    //District Thana
    Route::get('district', 'API\V1\DistrictThanaController@district');
    Route::get('thana/{districtId}', 'API\V1\DistrictThanaController@thana');

    #SMS
    Route::post('send-sms', 'API\V1\SmsController@sendSms');

    //Dynamic Page
    Route::get('dynamic-page/{slug}', 'API\V1\DynamicPageController@getDynamicPageData');

    //Ethics & Compliance
    Route::get('ethics-and-compliance', 'API\V1\EthicsController@index');

    //Media Landing Page
    Route::get('media-landing-page', 'API\V1\MediaController@getComponents');

    //Media Press Release
    Route::get('media-press-release', 'API\V1\MediaController@getPressRelease');
    Route::get('media-press-release/filter/{from}/{to}', 'API\V1\MediaController@getPressReleaseFilter');

    //Media News Event
    Route::get('media-news-event', 'API\V1\MediaController@getNewsEvent');
    Route::get('media-news-event/filter/{from}/{to}', 'API\V1\MediaController@getNewsEventFilter');

    //Media TVC Video
    Route::get('media-tvc-video', 'API\V1\MediaController@getTvcVideoData');

    // FAQ
    Route::get('faq/{slug}', 'API\V1\FaqController@getFAQ');

    // 4G Internet Offers
    Route::get('four-g-internet/{package_type}', 'API\V1\BanglalinkFourGController@getFourGInternet');

    // 4G Devices
    Route::get('four-g-devices', 'API\V1\BanglalinkFourGController@getFourGDevices');

    // 4G Campaign
    Route::get('four-g-campaign', 'API\V1\BanglalinkFourGController@getCampaignWithBanner');

    // 4G Status Check
    Route::get('four-g-usim-eligibility/{msisdn}', 'API\V1\BanglalinkFourGController@checkUSIMEligibility');

    // Be A Partner
    Route::get('be-a-partner', 'API\V1\BeAPartnerController@getBeAPartner');

    // Customer Feedback
    Route::get('customer-feedback/questions', 'API\V1\CustomerFeedbackController@getQuestionAns');
    Route::post('customer-feedback/save', 'API\V1\CustomerFeedbackController@customerFeedbackSave');

    // Banglalink 3G
    Route::get('banglalink-three-g', 'API\V1\BanglalinkThreeGController@getThreeGData');

    // Corporate Responsibility
    Route::get('corporate-responsibility/section', 'API\V1\CorporateResponsibilityController@getSection');
    //CR-strategy Corporate Responsibility
    Route::get('corporate-cr-strategy', 'API\V1\CorporateResponsibilityController@getCrStrategySection');
    Route::get('corporate-cr-strategy/details-components/{url_slug}', 'API\V1\CorporateResponsibilityController@getCrStrategyDetailsComponents');

    // Case Study
    Route::get('corp-case-study-report/section-component', 'API\V1\CorporateResponsibilityController@getCaseStudySection');
    Route::get('corporate-case-study/details-components/{url_slug}', 'API\V1\CorporateResponsibilityController@getCaseStudyDetailsComponents');

    // Initiative Tab
    Route::get('corporate/initiative-tabs', 'API\V1\CorporateResponsibilityController@getInitiativeTabs');

    // Initiative Component
    Route::get('corporate/initiative-tabs/component/{url_slug}', 'API\V1\CorporateResponsibilityController@getInitiativeTabComponent');

    //Corporate Res Contact Info Save
    Route::post('corporate/contact-info-save', 'API\V1\CorporateResponsibilityController@getContactInfoSave');

    // Referral Get Code
    Route::get('referral-code/{mobileNo}/{app_id}', 'API\V1\AppServiceDetailsController@getReferralCode');
    Route::post('referral-code/share', 'API\V1\AppServiceDetailsController@shareReferralCode');

    //Image File Viewer
    Route::get('test-offers', 'API\V1\ImageFileViewerController@offerList');
//    Route::get('show-file/{dirLocation}/{fileName}', 'API\V1\FileViewController@showFile');

    // SEO Image URL generator test
    Route::get('banner-image/web/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImageWeb');
    Route::get('banner-image/mobile/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImageMobile');
    Route::get('content-image/{model}/{fileName}', 'API\V1\ImageFileViewerController@contentIamge');

    Route::get('fixed-page/meta-tag/{key}', 'API\V1\FixedPageMateTagController@getFixedMateTag');

    // Token generator
    Route::get('secret-token', 'API\V1\SecreteTokenController@getToken');

    // Upsell FB
    
    // Phase 1
    Route::post('upsell/request-purchase', 'API\V1\UpsellFacebook\UpsellController@requestPurchase');//->middleware('verifyIdpToken');

    // Phase 2
    Route::post('upsell/purchase-product', 'API\V1\UpsellFacebook\UpsellController@purchaseProduct');//->middleware('verifyFacebookUpsellKey');
});


//Route::get('/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImageWeb');
