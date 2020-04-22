<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use Illuminate\Database\QueryException;

/**
 * Class SslCommerzService
 * @package App\Services
 */
class SslCommerzService extends ApiBaseService
{
    /**
     * SslCommerzService constructor.
     */
    public function __construct()
    {
        $this->base_url = url('/') . '/api/v1';
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeViaSsl($request)
    {
        $url = env('STORE_URL');
        $store_id = env('STORE_ID');
        $store_passwd = env('STORE_PASSWORD');

        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = env('STORE_URL');
        $requestData = $this->getFormattedData($request->all());
        $returnResult = $this->callToApiAction($requestData, $setLocalhost = true, $direct_api_url);

        $sslReturnResult = json_decode($returnResult, true);

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

        try {
            if (isset($response)) {
                return $this->sendSuccessResponse($response, 'Top up done', [], HttpStatusCode::SUCCESS);
            }
            return $this->sendErrorResponse('Data Not Found!', [], HttpStatusCode::NOT_FOUND);
        } catch (QueryException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($request)
    {
        $response = request()->all();
        try {
            if (isset($response)) {
                return $this->sendSuccessResponse($response, 'Top up done', [], HttpStatusCode::SUCCESS);
            }
            return $this->sendErrorResponse('Data Not Found!', [], HttpStatusCode::NOT_FOUND);
        } catch (QueryException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
        //return redirect('http://172.16.229.242/en/payment-success');
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function failure($request)
    {
        $response = request()->all();

        try {
            if (isset($response)) {
                return $this->sendSuccessResponse($response, 'Top up Failed', [], HttpStatusCode::SUCCESS);
            }
            return $this->sendErrorResponse('Data Not Found!', [], HttpStatusCode::NOT_FOUND);
        } catch (QueryException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
        // return redirect('http://172.16.229.242/en/payment-fail');
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($request)
    {
        $response = request()->all();
        try {
            if (isset($response)) {
                return $this->sendSuccessResponse($response, 'Top up Canceled', [], HttpStatusCode::SUCCESS);
            }
            return $this->sendErrorResponse('Data Not Found!', [], HttpStatusCode::NOT_FOUND);
        } catch (QueryException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
        //return redirect('http://172.16.229.242/en/payment-cancel');
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRequestDetails($request)
    {
        $data = $request->all();

        $url = config('apiurl.ssl_api_host') . '/initiate-recharge';
        $transactionId = $this->generateTransactionId();
        $responseData = [
            'url' => $url,
            'transactionId' => $transactionId,
        ];
        //TODO: Save ssl transaction details

        return $this->sendSuccessResponse($responseData, 'Payment Option', [], HttpStatusCode::SUCCESS);
    }

    /**
     * @return string
     */
    public function generateTransactionId()
    {
        return uniqid('BLWN');
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentRequestSubmit($request)
    {
        $data = $request->all();
        $data['tran_id'] = $this->generateTransactionId();

        $response['url'] = env('APP_URL') . "/payment-view?amount=" . $data['amount'] .
            "&topup_number=" . $data['topup_number'] . "&connection_type=" . $data['connection_type']
            . "&email=" . $data['email'] . "&tran_id=" . $data['tran_id'];

        return $this->sendSuccessResponse($response, 'Payment Link', [], HttpStatusCode::SUCCESS);
    }


    /**
     * Format Request Data
     *
     * @param $data
     * @return array
     */
    public function getFormattedData($data)
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
        $post_data['cus_email'] = "test@gmail.com";

        $post_data['cus_add1'] = isset($data['cus_add1']) ? $data['cus_name'] : 'N/A';
        $post_data['cus_city'] = "N/A";
        $post_data['cus_postcode'] = "N/A";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = isset($data['cus_phone']) ? $data['cus_phone'] : 'N/A';

        # PRODUCT INFORMATION
        $post_data['product_name'] = "TOP UP";
        $post_data['product_category'] = "top up";
        $post_data['product_profile'] = "telecom-vertical ";
        $post_data['product_type'] = isset($data['product_type']) ? $data['product_type'] : 'Recharge';
        $post_data['topup_number'] = $data['topup_number'];
        $post_data['country_topup'] = "Bangladesh";


        # SHIPMENT INFORMATION
        $post_data['shipping_method'] = "NO";
        $post_data['num_of_item'] = isset($data['num_of_item']) ? $data['num_of_item'] : 1;

        # EMI STATUS
        $post_data['emi_option'] = "0";

        # CART PARAMETERS
        $post_data['cart'] = json_encode(isset($data['cart']) ? $data['cart'] : 1);
        $post_data['product_amount'] = isset($data['product_amount']) ? $data['product_amount'] : 1;

        return $post_data;
    }


    /**
     * Initiate Payment Request
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePaymentRequest($request)
    {
        $msisdn = "";
        $type = "";
        $amount = "";
        $total_amount = 0;
        $email = $request->has('email') ? $request->email : "";

        foreach ($request->recharge_number as $key => $item) {
            $msisdn .= "msisdn[$key]=" . $item['topup_number'] . '&';
            $type .= "connection_type[$key]=" . $item['connection_type'] . '&';
            $amount .= "amount[$key]=" . $item['amount'] . '&';
            $total_amount += $item['amount'];
        }

        $tran_id = $this->generateTransactionId();

        $base_url = env('SSL_API_HOST') . "/create-recharge";
        $url = $base_url . "?" . $msisdn . $type . $amount .
            "trns_id=" . $tran_id . "&email=$email&cus_name=&card_name=&total_amount=" . $total_amount;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        if ($err) {
            return $this->sendErrorResponse(
                "Curl exception error",
                [
                    'message' => 'Something went wrong.Payment gateway connection failed'
                ],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        $result = json_decode($response, true);
        if ($result['code'] != 200) {
            return $this->sendErrorResponse(
                $result['message'],
                [
                    'message' => 'Something went wrong.Payment gateway connection failed'
                ],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        return $this->sendSuccessResponse($result['data'], 'Initiate Payment', [], HttpStatusCode::SUCCESS);
    }

    /*    public function initiatePaymentRequest($request)
        {
            $amount = $request->input('amount');
            $number = $request->input('topup_number');
            $type = $request->input('connection_type');
            $email = $request->input('email');
            $tran_id = $this->generateTransactionId();

            $base_url = env('SSL_API_HOST') . "/create-recharge";
            $url = $base_url . "?msisdn[0]=" . $number . "&connection_type[0]=" . $type . "&amount[0]=" . $amount .
                "&trns_id=" . $tran_id . "&email=" . $email . "&cus_name=Test&card_name=&total_amount=" . $amount;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                return $this->sendErrorResponse(
                    "Curl exception error",
                    [
                        'message' => 'Something went wrong.Payment gateway connection failed'
                    ],
                    HttpStatusCode::INTERNAL_ERROR
                );
            }

            $result = json_decode($response, true);
            if ($result['code'] != 200) {
                return $this->sendErrorResponse(
                    $result['message'],
                    [
                        'message' => 'Something went wrong.Payment gateway connection failed'
                    ],
                    HttpStatusCode::INTERNAL_ERROR
                );
            }

            return $this->sendSuccessResponse($result['data'], 'Initiate Payment', [], HttpStatusCode::SUCCESS);
        }*/


    /**
     * @param $data
     * @param  bool  $setLocalhost
     * @param  string  $direct_api_url
     * @return bool|string
     */
    public function callToApiAction($data, $setLocalhost = false, $direct_api_url = '')
    {
        $header = array();
        $curl = curl_init();


        if (!$setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
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
}
