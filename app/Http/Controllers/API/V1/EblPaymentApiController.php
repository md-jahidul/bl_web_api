<?php

namespace App\Http\Controllers\API\V1;

use App\Eblskypay\Skypay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class EblPaymentApiController extends Controller
{

    public function configaration(){
        $configArray = array();
        $configArray["gatewayMode"] = FALSE;
        $configArray["certificateVerifyPeer"] = FALSE;
        $configArray["certificateVerifyHost"] = 0;
        $configArray["merchantId"] = "20070005";
        $configArray["password"] = "cd8f2ab3946fe668e25492051e5b2b01";
        $configArray["debug"] = FALSE;
        $configArray["version"] = "52";
        return $configArray;
    }

    public function postData(){
        $orderID = "EBLSPD".time();
//        $configArray = $this->configaration();

        $postData = array();
        $postData['order']['id'] = $orderID;
        $postData['order']['amount'] = "5000";
        $postData['order']['currency'] = "BDT";
        $postData['order']['description'] = "EBL SKYPAY DEMO";
        $postData['interaction']['cancelUrl'] = "http://172.16.229.242/api/v1/ebl-pay/cancel";
        $postData['interaction']['returnUrl'] = "http://172.16.229.242/api/v1/ebl-pay/complete/$orderID";
        $postData['interaction']['operation'] = "PURCHASE";
        $postData['interaction']['timeout']   = "600";
        $postData['interaction']['merchant']['name']   = "EBL SKYPAY DEMO";
        $postData['interaction']['merchant']['logo']   = "https://placeimg.com/300/140/tech";
        $postData['interaction']['displayControl']['billingAddress']   = "HIDE";
        $postData['interaction']['displayControl']['orderSummary']   = "HIDE";


//        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['order']['id']) && isset($_POST['order']['amount']) && isset($_POST['order']['currency'])) {
        $skypay = new Skypay($this->configaration());

        $responseArray = $skypay->Checkout($postData);

        return $responseArray;
//        print_r(json_encode($responseArray));
//        }

    }


    public function complete($orderID)
    {
//        echo "<pre>";
//        print_r($_GET);die();

        $errorMessage = "";
        $errorCode = "";
        $gatewayCode = "";
        $result = "";

        $responseArray = array();

        $resultIndicator =  (isset($_GET["resultIndicator"]))?$_GET["resultIndicator"]:"";


        $eblskypay = session()->get('eblskypay');

        if( !empty($eblskypay['successIndicator']) && ($eblskypay['successIndicator'] == $resultIndicator) ) {
            $skypay = new skypay($this->configaration());
            $responseArray = $skypay->RetrieveOrder($orderID);
            if(($responseArray["amount"] == $responseArray["totalAuthorizedAmount"]) && ($responseArray["amount"] == $responseArray["totalCapturedAmount"])) {
                $result = "Payment Completed";
            }
        }
        return $responseArray;
    }

    public function cancel()
    {
        echo "Transaction Cancel";
    }



}
