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
        $this->base_url =  'http://localhost:3030';// url('/');
    }

    public function apiFormatter($response)
    {
        try{
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
        }catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                ]
            );
        }
    }

    public function getPostData()
    {
        $post_data = array();
        $post_data['store_id'] = "bangl5da2f2be91898";
        $post_data['store_passwd'] = "bangl5da2f2be91898@ssl";
        $post_data['total_amount'] = "15000";
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid();
        $post_data['success_url'] =  $this->base_url . "/en/payment-success";
        $post_data['fail_url'] =  $this->base_url . "/en/payment-fail";
        $post_data['cancel_url'] = $this->base_url . "/en/cancel";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = "Jahidul Islam";
        $post_data['cus_email'] = "jahidul@gmail.com";
        $post_data['cus_add1'] = "Dhaka";
        $post_data['cus_add2'] = "Dhaka";
        $post_data['cus_city'] = "Dhaka";
        $post_data['cus_state'] = "Dhaka";
        $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = '0188376655';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1 '] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_country'] = "Bangladesh";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b '] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        # EMI STATUS
        $post_data['emi_option'] = "1";

        # CART PARAMETERS
        $post_data['cart'] = json_encode(array(
            array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
        ));
        $post_data['product_amount'] = "100";
        $post_data['vat'] = "5";
        $post_data['discount_amount'] = "5";
        $post_data['convenience_fee'] = "3";

        return $post_data;
    }

    public function ssl()
    {
//     <----------cred: Banglalink account------------->
        $url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
        $store_id = 'bangl5da2f2be91898';
        $store_passwd = 'bangl5da2f2be91898@ssl';

        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = $url;
        $returnResult= $this->calltoapiAction($this->getPostData(),$setLocalhost = true,$direct_api_url);


        $sslReturnResult = json_decode($returnResult, true);
        $sessionkey='';
        $GatewayURL='';
        $GatewayStatus='200';
        if($sslReturnResult['status']=='SUCCESS'){
            $sessionkey=$sslReturnResult['sessionkey'];
            $GatewayURL=$sslReturnResult['GatewayPageURL'];
            $GatewayStatus='100';
        }

        $request = array(
            'url' => $url,
            'total_amount' => '5',
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'tran_id' => 1
        );

        $response = new \stdClass();
        $response->response_code =(!empty($orderId))?100:400;
        $response->errors = array();
        $request['sessionkey'] = $sessionkey;
        $request['gateway_url'] = $GatewayURL;
        $request['gateway_status'] = $GatewayStatus;
        $request['gateway_connect_status'] = $sslReturnResult['status'];
        $response->data = $request;

//        $this->apiFormatter($response);

        try{
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
        }catch (QueryException $e) {
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

    public function calltoapiAction($data,$setLocalhost = false,$direct_api_url=''){
        $header=array();
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




    public function success(Request $request){
        $successData = request()->all();
        $this->apiFormatter($successData);
    }

    public function failure(Request $request){

        $failureData = request()->all();
        $this->apiFormatter($failureData);
    }

    public function cancel(Request $request){
        $cancelData = request()->all();
        $this->apiFormatter($cancelData);
    }

}
