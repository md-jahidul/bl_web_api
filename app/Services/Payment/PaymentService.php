<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/27/19
 * Time: 12:00 PM
 */

namespace App\Services\Payment;


use App\Enums\HttpStatusCode;
use App\Repositories\RechargeLogRepository;
use App\Services\ApiBaseService;
use App\Services\NumberValidationService;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Exception\ClientException;


class PaymentService extends ApiBaseService
{
    /**
     * @var PaymentService
     */
    private $paymentBaseService;

    protected const OWN_PAYMENT_GATEWAYS_END_POINT = "/api/v1/payment-gateways";
    protected const OWN_PAYMENT_END_POINT = "/api/v1/pay";
    protected const SSL_PAYMENT_GATEWAYS_END_POINT = "/available-card";

    /**
     * PaymentService constructor.
     */
    public function __construct(
        //
    ) {
        //
    }

    /**
     * @return JsonResponse|mixed
     */
    public function paymentGateways()
    {
        $sslRgwPaymentGateways = $this->sslPaymentGateways();
        $ownRgwPaymentGateways = $this->ownRgwPaymentGateways();
        $data = [
            'own_rgw' => is_array($ownRgwPaymentGateways) ? $ownRgwPaymentGateways : $ownRgwPaymentGateways->getData(),
            'ssl_rgw' => is_array($sslRgwPaymentGateways) ? $sslRgwPaymentGateways : $sslRgwPaymentGateways->getData()
        ];
        return $this->sendSuccessResponse($data, 'Payment gateways!!');
    }

    public function ownRgwPaymentGateways()
    {
        $baseURL = env('OWN_RGW_API_HOST', 'https://pay-test.banglalink.net');
        $header = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json',
                'cl-id' => env('OWN_RGW_CLIENT_ID'),
                'cl-secret' => env('OWN_RGW_CLIENT_SECRET'),
            ]
        ];

        try {
            $client = new Client(["base_uri" => $baseURL]);
            $response = $client->get(self::OWN_PAYMENT_GATEWAYS_END_POINT, $header)->getBody()->getContents();
            $response = json_decode($response, true);
            return $response['data'];
        } catch (\Exception $exception) {
            Log::channel('paymentReqLog')->info('pgw_payment_gateway_error : ' . $exception->getMessage());
            return $this->sendErrorResponse('Internal server Error', $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @return JsonResponse|mixed|void
     */
    public function sslPaymentGateways()
    {
        $baseURL = env('SSL_API_HOST', 'https://core.easy.com.bd/api/v1/blweb');

        $header = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json',
            ]
        ];

        try {
            $client = new Client(["base_uri" => $baseURL]);
            $response = $client->get(self::SSL_PAYMENT_GATEWAYS_END_POINT, $header);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (\Exception $exception) {
            Log::channel('paymentReqLog')->info('ssl_payment_gateway_error : ' . $exception->getMessage());
            return $this->sendErrorResponse('Internal server Error', "PGW couldn't perform", $exception->getCode());
        }
    }

    public function ownRgwPayment($data)
    {
        $baseURL = env('OWN_RGW_API_HOST', 'https://pay-test.banglalink.net');
        //  $validatedCashbackAndIris = $this->getCashbackAndIrisMapping($data);
        //  $curatedPaymentData = $this->curePaymentData($data, $validatedCashbackAndIris);
        //  $data['recharge_data'] = $data;
        //  $data['requester_msisdn'] = $requesterUserMsisdn;
        //  dd($data['recharge_data']);
        $client = new Client(["base_uri" => $baseURL]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json',
                'cl-id' => env('OWN_RGW_CLIENT_ID'),
                'cl-secret' => env('OWN_RGW_CLIENT_SECRET'),
            ],
            'json' => $data
        ];

        try {
            $response = $client->post(self::OWN_PAYMENT_END_POINT, $options)->getBody()->getContents();
            $this->logToDbInitiatePayment($data, $response);
            $this->logToFile($options, $response);
            return json_decode($response, true);
        } catch (\Exception $exception) {
            Log::channel('paymentReqLog')->info('pgw_error : ' . $exception->getMessage());
            return $this->sendErrorResponse('Internal server error', "PGW couldn't perform", $exception->getCode());
        }
    }

    private function logToDbInitiatePayment($req, $res)
    {
        $res = is_array($res) ?  json_decode(json_encode($res)) : json_decode($res);

        $data = [
            // 'requester_msisdn' => $req['requester_msisdn'],
            'initiate_status_code' => $res->statusCode,
            'initiate_status' => $res->statusCode == 200 ? 'SUCCESSFUL' : 'FAILED',
            'trx_id' => $res->statusCode == 200 ? $res->data->tran_id : '',
            'gateway' => 'PGW',
            'channel' => $req['recharge_platform'],
            'recharge_amounts' => implode(',', collect($req['recharge_data'])->pluck('recharge_amount')->toArray()),
            'msisdns' => implode(',', collect($req['recharge_data'])->pluck('mobile_number')->toArray()),
            'total_payment_amount' => $res->statusCode == 200 ? $res->data->total_payment_amount : 0
        ];
        (new RechargeLogRepository)->create($data);
    }

    private function logToFile($req, $res)
    {
        Log::channel('pgwLogRec')->info('Req : ' . json_encode($req, JSON_PRETTY_PRINT) . 'Res : ' . $res);
    }
}
