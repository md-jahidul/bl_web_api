<?php


namespace App\Http\Controllers\API\V1\MyPlan;

use Illuminate\Http\Request;
use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Exceptions\TokenInvalidException;
use App\Services\MyPlan\MyPlanProductService;

class MyPlanProductController extends Controller
{
    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var MyPlanProductService
     */
    protected $myblPlanService;


    public function __construct(
        MyPlanProductService $myblPlanService,
        CustomerService      $customerService,
        ApiBaseService       $apiBaseService
    ) {
        $this->myblPlanService = $myblPlanService;
        $this->customerService = $customerService;
        $this->apiBaseService = $apiBaseService;
    }

    public function getMyPlanProducts(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            throw new TokenInvalidException();
        }

        if ($customer->number_type == "postpaid") {
            return $this->apiBaseService->sendErrorResponse('Plan Product not available for postpaid users', [], HttpStatusCode::BAD_REQUEST);
        }

        try {
            $myBlPlanProducts = $this->myblPlanService->getMyPlanProducts();
            return $this->apiBaseService->sendSuccessResponse($myBlPlanProducts, 'MyBlPlan Products');
        } catch (\Exception $e) {
            return $this->apiBaseService->sendErrorResponse('Failed to retrive plan products', [$e->getMessage()], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
