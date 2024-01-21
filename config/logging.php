<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
        'lever_api_log' => [
            'driver' => 'single',
            'path' => storage_path('logs/lever_api.log'),
            'level' => 'info',
        ],
        'amarOffer' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/amarOffer/amar_offer_log.log'),
            'level' => 'info',
            'days' => 5,
        ],
        'pgwLogRec' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/pgw_log/pgw_request_log.log'),
            'level' => 'info',
        ],
        'sslReqLog' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/ssl_log/request_log.log'),
            'level' => 'info',
        ],
        'mailSendFailLog' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/bl-lab/mail-send/send_fail.log'),
            'level' => 'error',
        ],
        'ideaSubmitLog' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/bl-lab/idea-submit-fail/submission_fail.log'),
            'level' => 'error',
        ],
        'blLabRegFail' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/bl-lab/reg-fail/reg_fail.log'),
            'level' => 'error',
        ],
        'apihub-error' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/apihub/apihub-error.log'),
            'level' => 'info',
            'days' => 5,
        ],
        'MyPlan' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/misc/my_plan.log'),
            'level' => 'info',
            'days' => 7,
        ],
        'clientSecretToken' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/invalid-request/invalid-req-error.log'),
            'level' => 'error',
        ],
        'apiHubReqError' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/apihub/api-hub-error.log'),
            'level' => 'info',
        ],
    ],
];
