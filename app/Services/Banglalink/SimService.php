<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SimService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_INFO_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getSimInfoUrl($customer_id)
    {
        return self::CUSTOMER_INFO_API_ENDPOINT . '/' . $customer_id . '/sim-cards';
    }

    private function getAuthenticateUser($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];


        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);


        if ($data['token_status'] != 'Valid') {
            return $this->responseFormatter->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        return $user;
    }

    public function getSimInfo(Request $request)
    {
        $user = $this->getAuthenticateUser($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $response = $this->get($this->getSimInfoUrl($user->id));

        $response = json_decode($response['response']);
        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse($response->message, [], $response->status);
        }

        $data = [];
        $connections = [];
        foreach ($response as $sim) {
            $connections [] = [
                'id' => $sim->id,
                'type' => $sim->simType,
                "mobile_number" => "01937505779",
                "msisdn" => "01937505779",
                'puk1' => $sim->puk1,
                'puk2' => $sim->puk2,
                'pin1' => $sim->pin1,
                'pin2' => $sim->pin2,
                'icc' => $sim->icc,
                'status' => $sim->status,
                "bar_status" => (bool)random_int(0, 1),
                'active_since' => Carbon::createFromDate('2018', rand(1, 12), rand(1, 20))
                                   ->toDateTimeString(),
                "package" => "Banglalink Desh Ek Rate Darun",
            ];
        }

        $data ['name'] = "Saif Karim";
        $data ['nid'] = "212384717263";
        $data ['address'] = "House # 23, Road # 8A, Dhanmondi, Dhaka PostCode # 1206";
        $data ['sim'] = $connections;
        return $this->responseFormatter->sendSuccessResponse($data, 'Sim Information retrieved');
    }
}
