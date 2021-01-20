<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'internal' => [
            'driver' => 'local',
            'root' => env('UPLOAD_BASE_PATH', '/src/uploads'),
            'url' => env('APP_URL') . '/uploads',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],
    'image_host_url' => env('IMAGE_HOST_URL', 'http://172.16.8.160:8443/uploads/'),
    'image_host' => env('IMAGE_HOST', 'http://172.16.8.160:8443'),

    // File Location
    'moduleType' => [
        /* Preapaid, Postpaid Module */
        'OfferCategory' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_image_url',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'offer-category',
            'model' => 'OfferCategory',
        ],

        /* Roaming Module */
        'RoamingCategory' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_web',
            'exact_path_mobile' => 'banner_mobile',
            'model-key' => 'roaming-category',
            'model' => 'RoamingCategory'
        ],
        'RoamingOtherOffer' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_web',
            'exact_path_mobile' => 'banner_mobile',
            'model-key' => 'roaming-other-offer',
            'model' => 'RoamingOtherOffer'
        ],
        'RoamingInfo' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_web',
            'exact_path_mobile' => 'banner_mobile',
            'model-key' => 'roaming-info-tips',
            'model' => 'RoamingInfo'
        ],

        /* App & Service Module */
        'AppServiceTab' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_image_url',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'app-service-tab',
            'model' => 'AppServiceTab'
        ],
        'AppServiceProduct' => [
            'image_name_en' => 'product_img_en',
            'image_name_bn' => 'product_img_bn',
            'exact_path_web' => 'product_img_url',
            'exact_path_mobile' => 'product_img_url',
            'model-key' => 'app-service-product',
            'model' => 'AppServiceProduct'
        ],
        'AppServiceProductDetail' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'image',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'app-service-product-detail',
            'model' => 'AppServiceProductDetail',
        ],

        /* Business Module */
        'BusinessCategory' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_photo',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'business-category',
            'model' => 'BusinessCategory'
        ],
        'BusinessNews' => [
            'image_name_en' => 'image_name_en',
            'image_name_bn' => 'image_name_en',
            'exact_path_web' => 'image_url',
            'exact_path_mobile' => 'image_url',
            'model-key' => 'business-news',
            'model' => 'BusinessNews'
        ],
        'BusinessHomeBanner' => [
            'image_name_en' => 'image_name_en',
            'image_name_bn' => 'image_name_bn',
            'exact_path_web' => 'image_name',
            'exact_path_mobile' => 'image_name_mobile',
            'model-key' => 'business-home-banner',
            'model' => 'BusinessHomeBanner'
        ],
        'BusinessPackages' => [
            'image_name_en' => 'card_banner_name_en',
            'image_name_bn' => 'card_banner_name_bn',
            'exact_path_web' => 'card_banner_web',
            'exact_path_mobile' => 'card_banner_mobile',
            'model-key' => 'business-package',
            'model' => 'BusinessPackages'
        ],
        'BusinessPackageDetails' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_photo',
            'exact_path_mobile' => 'banner-image-mobile',
            'model-key' => 'business-package-details',
            'model' => 'BusinessPackages'
        ],
        'BusinessOthers' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_photo',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'business-others',
            'model' => 'BusinessOthers'
        ],
        'BusinessOtherDetails' => [
            'image_name_en' => 'details_banner_name',
            'image_name_bn' => 'details_banner_name_bn',
            'exact_path_web' => 'details_banner_web',
            'exact_path_mobile' => 'details_banner_mobile',
            'model-key' => 'business-other-details',
            'model' => 'BusinessOthers'
        ],
        'BusinessInternet' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_photo',
            'exact_path_mobile' => 'banner_image_mobile',
            'model-key' => 'business-internet',
            'model' => 'BusinessInternet'
        ],

        /* Corporate Responsibility Module */
        'CorpCrStrategyComponent' => [
            'image_name_en' => 'image_name_en',
            'image_name_bn' => 'image_name_bn',
            'exact_path_web' => "image_base_url",
            'exact_path_mobile' => null,
            'model-key' => 'corp-cr-strategy-component',
            'model' => 'CorpCrStrategyComponent',
//            'component_page_type' => 'cr_strategy_component',
            'image_type' => 'body-image'
        ],

        'CorpCrStrategyDetailsComponent' => [
            'image_name_en' => 'image_name_en',
            'image_name_bn' => 'image_name_bn',
            'exact_path_web' => 'image',
            'exact_path_mobile' => null,
            'model-key' => 'corp-cr-strategy-details-component',
            'model' => 'Component',
            'component_page_type' => 'cr_strategy_component_details',
            'image_type' => 'body-image'
        ],

        'CorpCrStrategyDetailsComponentMultiImg' => [
            'image_name_en' => 'img_name_en',
            'image_name_bn' => 'img_name_bn',
            'exact_path_web' => 'base_image',
            'exact_path_mobile' => null,
            'model-key' => 'corp-cr-strategy-details-component-multi-img',
            'model' => 'ComponentMultiData',
            'component_page_type' => 'cr_strategy_component_details',
            'image_type' => 'body-image'
        ],

        'Priyojon' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_image_url',
            'exact_path_mobile' => 'banner_mobile_view',
            'model-key' => 'priyojon',
            'model' => 'Priyojon'
        ],
        'PartnerOfferDetail' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_image_url',
            'exact_path_mobile' => 'banner_mobile_view',
            'model-key' => 'partner-offer-details',
            'model' => 'PartnerOfferDetail'
        ],
        'LmsAboutBannerImage' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'banner_image_url',
            'exact_path_mobile' => 'banner_mobile_view',
            'model-key' => 'lms-about-banner',
            'model' => 'LmsAboutBannerImage'
        ],
        'AboutPageLeftImg' => [
            'image_name_en' => 'left_img_name_en',
            'image_name_bn' => 'left_img_name_bn',
            'exact_path_web' => 'left_side_img',
            'exact_path_mobile' => '',
            'model-key' => 'about-page-left',
            'model' => 'AboutPage'
        ],
        'AboutPageRightImg' => [
            'image_name_en' => 'right_img_name_en',
            'image_name_bn' => 'right_img_name_bn',
            'exact_path_web' => 'right_side_ing',
            'exact_path_mobile' => '',
            'model-key' => 'about-page-right',
            'model' => 'AboutPage'
        ],
        'EcareerPortal' => [
            'image_name_en' => 'banner_name',
            'image_name_bn' => 'banner_name_bn',
            'exact_path_web' => 'image',
            'exact_path_mobile' => 'image_mobile',
            'model-key' => 'ecareer-portal',
            'model' => 'EcareerPortal'
        ],
        'EcareerPortalItem' => [
            'image_name_en' => 'image_name',
            'image_name_bn' => 'image_name_bn',
            'exact_path_web' => 'image',
            'exact_path_mobile' => '',
            'model-key' => 'ecareer-portal-item',
            'model' => 'EcarrerPortalItem'
        ]

    ],

    'modelKeyList' => [
        'offer-category' => 'OfferCategory',
        'roaming-category' => 'RoamingCategory',
        'roaming-other-offer' => 'RoamingOtherOffer',
        'roaming-info-tips' => 'RoamingInfo',
        'app-service-product' => 'AppServiceProduct',
        'app-service-tab' => 'AppServiceTab',
        'app-service-product-detail' => 'AppServiceProductDetail',
        'business-category' => 'BusinessCategory',
        'business-news' => 'BusinessNews',
        'business-home-banner' => 'BusinessHomeBanner',
        'business-package' => 'BusinessPackages',
        'business-others' => 'BusinessOthers',
        'business-internet' => 'BusinessInternet',
        'business-other-details' => 'BusinessOtherDetails',
        'business-package-details' => 'BusinessPackages',
        'corp-cr-strategy-component' => 'CorpCrStrategyComponent',
        'corp-cr-strategy-details-component' => 'CorpCrStrategyDetailsComponent',
        'corp-cr-strategy-details-component-multi-img' => 'CorpCrStrategyDetailsComponentMultiImg',
        'priyojon' => 'Priyojon',
        'partner-offer-details' => 'PartnerOfferDetail',
        'lms-about-banner' => 'LmsAboutBannerImage',
        'about-page-left' => 'AboutPageLeftImg',
        'about-page-right' => 'AboutPageRightImg',
        'ecareer-portal' => 'EcareerPortal',
        'ecareer-portal-item' => 'EcareerPortalItem'
    ]
];
