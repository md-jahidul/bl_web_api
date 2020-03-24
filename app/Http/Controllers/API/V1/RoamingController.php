<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\RoamingService;
use Illuminate\Http\Request;
use DB;

class RoamingController extends Controller
{
    /**
     * @var $roammingService
     */
    protected $roammingService;
    /**
     * BusinessController constructor.
     * @param RoamingService $roammingService
     */
    public function __construct(RoamingService $roammingService)
    {
        $this->roammingService = $roammingService;
    }


    /**
     * Get category list
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 23/03/2020
     */
    public function getCategories()
    {
        return $this->roammingService->getCategories();
    }

    /**
     * Get Country List
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/03/2020
     */
    public function getCountries()
    {
        return $this->roammingService->getCountries();
    }
    /**
     * Get Operators List
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/03/2020
     */
    public function getOperators($countryEn)
    {
        return $this->roammingService->getOperators($countryEn);
    }
    /**
     * Get Operators List
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/03/2020
     */
    public function roamingGeneralPage($pageSlug)
    {
        return $this->roammingService->roamingGeneralPage($pageSlug);
    }

}
