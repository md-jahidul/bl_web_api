<?php

namespace App\Helpers;

use App\Models\SmsLanguage;

class SmsLanguageHelper
{
    private const CUSTOMER_LANG_URL = '/customer-information/customer-information/get-language/';

    /**
     * @param $feature
     * @param array $vars
     * @param string $lang
     * @return string
     */
    public static function getSmsText($feature, $vars = [], $lang = 'bn'): string
    {
        $smsConfig = SmsLanguage::select('sms_bn', 'sms_en', 'concat_char')
            ->where('feature', $feature)
            ->where('platform', 'assetlite')
            ->where('status', 1)
            ->first();

        if ($smsConfig) {
            $smsText = $lang === 'en' ? $smsConfig->sms_en : $smsConfig->sms_bn;
            $concatChar = $smsConfig->concat_char;
        } else {
            $smsText = self::getDefaultSmsText($feature);
            $concatChar = '$';
        }
        return self::prepareSMS($smsText, $vars, $concatChar);
    }

    /**
     * @param $text
     * @param array $vars
     * @param string $concatChar
     * @return string
     */
    public static function prepareSMS($text, $vars = [], $concatChar = '$') : string
    {
        $index = 0;
        while ($position = strpos($text, $concatChar)) {
            $replace = $vars[$index++];
            $text = substr_replace($text, $replace, $position, 1);
        }
        return $text;
    }

    /**
     * @param $feature
     * @return string
     */
    private static function getDefaultSmsText($feature): string
    {
        $sms = [
            config('constants.sms.features')[0] => "আপনার ওটিপিঃ $ । এই ওটিপির মেয়াদ $ মিনিটের মধ্যে শেষ হবে $",
//            config('constants.sms.features')[1] => "আপনার বন্ধু (88$) আপনাকে মাইবিএল অ্যাপ ডাউনলোড করতে আমন্ত্রণ জানাচ্ছেন। এখনই ডাউনলোড করুনঃ $ এবং $ কোডটি ব্যবহার করে পেয়ে যান $ বোনাস!",
        ];

        return $sms[$feature] ?? "";
    }

    /**
     * @param $msisdn
     * @return string
     */
    public static function getCustomerPreferredLanguage($msisdn): string
    {
        // $host = env('BL_API_HOST') ?? 'http://apigateway.banglalink.net:7171';
        $host = "http://172.16.254.122:8080";
        $url = $host . self::CUSTOMER_LANG_URL . "channel/MobileApp/msisdn/" . $msisdn;
        $lang = "bn";
        /**
         * Sending request to the api gateway
         */

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($curl));

        if (isset($result->status) && $result->status === 'success') {
            $lang = $result->lang ? 'en' : 'bn';
        }

        return $lang;
    }
}
