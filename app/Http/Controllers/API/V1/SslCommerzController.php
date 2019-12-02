<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SslCommerzController extends Controller
{

    protected $base_url = '';


    public function __construct()
    {
        $this->base_url = url('/') . '/api/v1'; // 'http://localhost:3030';
    }

    public function apiFormatter($response)
    {
        try {
            if (isset($response)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => $response
                    ]
                );
            }
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'message' => 'Data Not Found!'
                ]
            );
        } catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                ]
            );
        }
    }

    public function getPostData($data)
    {
        $post_data = array();
        $post_data['store_id'] = env('STORE_ID');
        $post_data['store_passwd'] = env('STORE_PASSWORD');
        $post_data['total_amount'] = $data['total_amount'];
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid();
        $post_data['success_url'] = $this->base_url . "/success";
        $post_data['fail_url'] = $this->base_url . "/failure";
        $post_data['cancel_url'] = $this->base_url . "/cancel";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = isset($data['cus_name']) ? $data['cus_name'] : 'N/A';
        $post_data['cus_email'] = "jahidul@gmail.com";

        $post_data['cus_add1'] = isset($data['cus_add1']) ? $data['cus_name'] : 'N/A';
        $post_data['cus_city'] = "N/A";
        $post_data['cus_postcode'] = "N/A";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = isset($data['cus_phone']) ? $data['cus_phone'] : 'N/A';

        # PRODUCT INFORMATION
        $post_data['product_name'] = "TOP UP";
        $post_data['product_category'] = "top up";
        $post_data['product_profile'] = "telecom-vertical ";
        $post_data['product_type'] = $data['product_type'];
        $post_data['topup_number'] = $data['topup_number'];
        $post_data['country_topup'] = "Bangladesh";


        # SHIPMENT INFORMATION
        $post_data['shipping_method'] = "NO";
        $post_data['num_of_item'] = $data['num_of_item'];

        # EMI STATUS
        $post_data['emi_option'] = "0";

        # CART PARAMETERS
        $post_data['cart'] = json_encode($data['cart']);
        $post_data['product_amount'] = $data['product_amount'];

        return $post_data;
    }

    public function ssl(Request $request)
    {
//     <----------cred: Banglalink account------------->
        $url = env('STORE_URL');
        $store_id = env('STORE_ID');
        $store_passwd = env('STORE_PASSWORD');

        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = $url;
        $requestData = $this->getPostData($request->all());
        $returnResult = $this->calltoapiAction($requestData, $setLocalhost = true, $direct_api_url);

        $sslReturnResult = json_decode($returnResult, true);
//        dd($sslReturnResult);
        $sessionkey = '';
        $GatewayURL = '';
        $GatewayStatus = '200';
        if ($sslReturnResult['status'] == 'SUCCESS') {
            $sessionkey = $sslReturnResult['sessionkey'];
            $GatewayURL = $sslReturnResult['GatewayPageURL'];
            $GatewayStatus = '100';
        }

        $request = array(
            'url' => $url,
            'total_amount' => '5',
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'tran_id' => 1
        );

        $response = new \stdClass();
        $response->errors = array();
        $request['sessionkey'] = $sessionkey;
        $request['gateway_url'] = $GatewayURL;
        $request['gateway_status'] = $GatewayStatus;
        $request['gateway_connect_status'] = $sslReturnResult['status'];
        $response->data = $request;

//        $this->apiFormatter($response);

        try {
            if (isset($response)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => $response
                    ]
                );
            }
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'message' => 'Data Not Found!'
                ]
            );
        } catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                ]
            );
        }

//        return response()->json($response);
    }

    public function calltoapiAction($data, $setLocalhost = false, $direct_api_url = '')
    {
        $header = array();
        $curl = curl_init();


        if (!$setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // The default value for this option is 2. It means, it has to have the same name in the certificate as is in the URL you operate against.
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // When the verify value is 0, the connection succeeds regardless of the names in the certificate.
        }

        curl_setopt($curl, CURLOPT_URL, $direct_api_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNo = curl_errno($curl);
        curl_close($curl);

        if ($code == 200 & !($curlErrorNo)) {
            return $response;
        } else {
            return "FAILED TO CONNECT WITH SSLCOMMERZ API";
        }
    }


    public function success(Request $request)
    {
        $successData = request()->all();
        $this->apiFormatter($successData);
        return redirect(env('ASSET_WEB_URL').'/payment-success');

    }

    public function failure(Request $request)
    {

        $failureData = request()->all();
        $this->apiFormatter($failureData);
        return redirect(env('ASSET_WEB_URL').'/payment-fail');

    }

    public function cancel(Request $request)
    {
        $cancelData = request()->all();
        $this->apiFormatter($cancelData);
        return redirect(env('ASSET_WEB_URL').'/payment-cancel');
    }

}
