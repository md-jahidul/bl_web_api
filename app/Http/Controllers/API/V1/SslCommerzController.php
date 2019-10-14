<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SslCommerzController extends Controller
{

    protected $base_url = 'http://localhost:8000';

    public function ssl()
    {

        // $gatewayurl = Mage::getStoreConfig('payment/synsslcompay/gatewayurl');

//     <----------cred: our old ssl account------------>
//        $url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
//        $store_id = 'silve5d873fa5b7245';
//        $store_passwd = 'silve5d873fa5b7245@ssl';


//     <----------cred: Banglalink account------------->
        $url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
        $store_id = 'bangl5da2f2be91898';
        $store_passwd = 'bangl5da2f2be91898@ssl';

        // $url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
        // $store_id = 'bs5d9f0b6f76bcf';
        // $store_passwd = 'bs5d9f0b6f76bcf@ssl';


        $post_data = array();
        $surl = $this->base_url . '/success';
        $furl = $this->base_url . '/failure';
        $caurl = $this->base_url . '/cancel';
        $version = "3.00";

        $post_data['total_amount'] = '10'; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

         # CUSTOMER INFORMATION
         $post_data['cus_name'] = 'Customer Name';
         $post_data['cus_email'] = 'customer@mail.com';
         $post_data['cus_add1'] = 'Customer Address';
         $post_data['cus_add2'] = "";
         $post_data['cus_city'] = "";
         $post_data['cus_state'] = "";
         $post_data['cus_postcode'] = "";
         $post_data['cus_country'] = "Bangladesh";
         $post_data['cus_phone'] = '8801XXXXXXXXX';
         $post_data['cus_fax'] = "";

          # SHIPMENT INFORMATION

        $post_data["shipping_method"] = "NO";

        $post_data["num_of_item"] = "1";
        $post_data['emi_option'] = "1";
        $post_data["product_category"] = "Online Payment";
        $post_data["product_name"] = "Online";
        $post_data['product_amount'] = '5';
        $post_data["product_profile"] = "general";
        $post_data["product_profile_id"] = "5";
        $post_data['discount_amount'] = "5";
        $post_data['vat'] = "5";
        $post_data['multi_card_name'] = "brac_visa,brac_master";
        $post_data["previous_customer"] = "Yes";
        $post_data["ipn_url"] = "https://developer.sslcommerz.com/doc/v4/";
        $post_data['tokenize_id'] = "1";
        $post_data["topup_number"] = '01711111111';
        $post_data["store_id"] = $store_id;
        $post_data["store_passwd"] =$store_passwd;
        $post_data['success_url']=$surl;
        $post_data['fail_url']=$furl;
        $post_data['cancel_url']=$caurl;

        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = $url;
        $returnResult= $this->calltoapiAction($post_data,$setLocalhost = true,$direct_api_url);
        // dd(json_decode($returnResult, true));
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
            'tran_id' => 1,
            'success_url' => $surl,
            'fail_url' => $furl,
            'cancel_url' => $caurl,
            'version' => $version,
        );

        $response = new \stdClass();
        $response->response_code =(!empty($orderId))?100:400;
        $response->errors = array();
        $request['sessionkey'] = $sessionkey;
        $request['gateway_url'] = $GatewayURL;
        $request['gateway_status'] = $GatewayStatus;
        $request['gateway_connect_status'] = $sslReturnResult['status'];
        $response->data = $request;

        return response()->json($response);
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
            //return "cURL Error #:" . $err;
        }
    }



    public function sslApi(){

        $post_data = array();
        $post_data['store_id'] = "bangl5da2f2be91898";
        $post_data['store_passwd'] = "bangl5da2f2be91898@ssl";
        $post_data['total_amount'] = "500";
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid();
        $post_data['success_url'] = "http://asset-lite-api.test/api/v1/success";
        $post_data['fail_url'] = "your payment application fail url";
        $post_data['cancel_url'] = "your payment application cancel url";

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





        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

# PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
            # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            echo "JSON Data parsing error!";
        }

    }

    public function success(Request $request){
        echo "Transaction success";
    }

}
