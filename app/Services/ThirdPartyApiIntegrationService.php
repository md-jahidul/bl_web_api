<?php
namespace App\Services;


class ThirdPartyApiIntegrationService
{
    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost() {
        return env('ASSET_HOST');
    }

    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getPort() {
        return env('ASSET_PORT');
    }


    /**
     * Get Token from env file
     *
     * @return string
     */
    public static function getToken() {
        return env('ASSET_TOKEN');
    }

    /**
     * Make the header array with authentication.
     *
     * @return array
     */
    private static function makeHeader()
    {
        return [
            'X-Client-Token: '.static::getToken(),
            'Content-Type: application/json',
            'Expect: 100-continue'
        ];
    }

    /**
     * Make CURL request for POST request.
     *
     * @param  string  $url
     * @param  array   $body
     * @param  array   $headers
     * @return string
     */
    public static function post($url, $body = [], $headers = null)
    {
        return static::makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param  string  $url
     * @param  array   $body
     * @param  array   $headers
     * @return string
     */
    public static function put($url, $body = [], $headers = [])
    {
        return static::makeMethod('put', $url, $body, $headers);
    }

    /**
     * Make CURL request for DELETE request.
     *
     * @param  string  $url
     * @param  array   $body
     * @param  array   $headers
     * @return string
     */
    public static function delete($url, $body = [], $headers = [])
    {
        return static::makeMethod('delete', $url, $body, $headers);
    }

    /**
     * Make CURL request for a HTTP request.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array   $body
     * @param  array   $headers
     * @return string
     */
    private static function makeMethod($method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: static::makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        static::makeRequest($ch, $url, $body, $headers);
        $result = curl_exec($ch);
        curl_close( $ch);
        return $result;
    }


    /**
     * Make CURL object for HTTP request verbs.
     *
     * @param  curl_init() $ch
     * @param  string  $url
     * @param  array   $body
     * @param  array   $headers
     * @return string
     */
    private static function makeRequest($ch, $url, $body, $headers)
    {

        $url = static::getHost().':'.static::getPort().$url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }


    /*public static function test()
      {
          $body = [
              "trigger" => "AT_SORTING",
              "timestamp" => "2017-10-23 11:46:20",
              "payload" => [
                  "collected" => 100,
                  "cost" => 80,
                  "total_fee" => 20,
                  "delivery_fee" => 15,
                  "cod_fee" => 5
              ],

              "meta" => [
                  "plan_id" => 1
              ],
          ];

          $res = static::post('/api/events', $body);

          return $res;
      }*/

}

//print_r(ThirdPartyApiIntegrationService::test());
