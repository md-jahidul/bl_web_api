<?php

return [
    'idp_host' => env('IDP_HOST', 'http://203.223.93.172:8443'),

    'idp_client_id' => env('IDP_CLIENT_ID', '8d253530-1b54-11ea-ab21-c50692967f59'),

    'idp_client_secret' => env('IDP_CLIENT_SECRET', 'puWyQvuHZTk9YEcuhkvYb7lSD7CVO0nZ0gdPfJf5'),

    'idp_otp_client_id' => env('IDP_OTP_CLIENT_ID', '0465bd90-1005-11ea-a2b6-efa1f2aff4ff'),

    'idp_otp_client_secret' => env('IDP_OTP_CLIENT_SECRET', 'zVN8wYHp5Ei5Wkyu6PT975tgMnWaYuCBY2tc7QqQ'),

    'bl_api_host' => env('BL_API_HOST', 'http://172.16.254.157:7081'),

    'ssl_api_host' => env('SSL_API_HOST', 'https://easy.com.bd/blweb/test'),

    'lever_api_host' => env('LEVER_API_HOST', 'https://api.lever.co/v0'),
    'lever_api_client' => env('LEVER_API_CLIENT', 'leverdemo'), // vimpelcom, leverdemo
];

?>
