<?php

namespace App\Services\Mybl;

use App\Repositories\AboutPageRepository;
use App\Repositories\LmsAboutBannerRepository;
use App\Repositories\LmsBenefitRepository;
use App\Repositories\PriyojonRepository;
use App\Services\ApiBaseService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;


class MyblAppCustomerService extends ApiBaseService
{
    /**
     * @var mixed
     */
    private $client;
    private const SEND_OTP = '/api/v1/send-otp';
    private const VERIFY_OTP = '/api/v1/verify-otp';
    private const ACCOUNT_DELETION_REASONS = '/api/v1/customers/delete-account';
    private const DELETE_TNC = '/api/v1/tnc/delete-account';
    private const CUSTOMER_ACCOUNT_DELETE = '/api/v1/customers/delete-account';

    /**
     * MyblAppCustomerService constructor.
     */
    public function __construct(
    ) {
        $this->client = new Client();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function otpRequest($request)
    {
        try {
            $url = config('mybl-app.base_url') . '/' . self::SEND_OTP;
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'phone' => $request->mobile
                ]
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            return $this->sendSuccessResponse($response['data'], 'OTP sent successfully');
        } catch (RequestException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json($errorResponse);
        }
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function otpVerify($request)
    {
        try {
            $url = config('mybl-app.base_url') . '/' . self::VERIFY_OTP;

            $body = $request->all();
            $body['client_id'] = config('mybl-app.client_id');
            $body['client_secret'] = config('mybl-app.client_secret');
            $body['grant_type'] = "otp_grant";
            $body['provider'] = "users";

            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $body
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            unset($response['data']['customer'], $response['data']['is_new_user']);

            return response()->json($response);
        } catch (RequestException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json($errorResponse);
        }
    }

    /**
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function feedbackQuestion()
    {
        try {
            $url = config('mybl-app.base_url') . '/' . self::ACCOUNT_DELETION_REASONS;
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ]);
            $response = json_decode($response->getBody()->getContents(), true);

            return response()->json($response);
        } catch (RequestException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json($errorResponse);
        }
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function deleteTnc()
    {
        try {
            $url = config('mybl-app.base_url') . '/' . self::DELETE_TNC;
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ]);
            $response = json_decode($response->getBody()->getContents(), true);

            return response()->json($response);
        } catch (RequestException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json($errorResponse);
        }
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function deleteAccount($request)
    {
        try {
            $url = config('mybl-app.base_url') . '/' . self::CUSTOMER_ACCOUNT_DELETE;
            $response = $this->client->request('DELETE', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $request->header('Authorization')
                ],
                'json' => $request->all()
            ]);
            $response = json_decode($response->getBody()->getContents(), true);

            return response()->json($response);
        } catch (RequestException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json($errorResponse);
        }
    }
}
