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
        MyPlanProductService $myblPlanService
    ) {
        $this->myblPlanService = $myblPlanService;
    }

    public function getMyPlanProducts(Request $request)
    {
        return $this->myblPlanService->getMyPlanProducts();
    }
}
