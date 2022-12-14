<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerOfferResource;
use App\Models\Priyojon;
use App\Services\AboutPageService;
use App\Services\PartnerOfferService;
use App\Services\PriyojonService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PriyojonController extends Controller
{

    /**
     * @var PartnerOfferService
     */
    private $partnerOfferService;
    private $aboutPriyojonService;
    /**
     * @var PriyojonService
     */
    private $priyojonService;

    /**
     * PriyojonController constructor.
     * @param PriyojonService $priyojonService
     * @param PartnerOfferService $partnerOfferService
     * @param AboutPageService $aboutPriyojonService
     */
    public function __construct(
        PriyojonService $priyojonService,
        PartnerOfferService $partnerOfferService,
        AboutPageService $aboutPriyojonService
    ) {
        $this->priyojonService = $priyojonService;
        $this->partnerOfferService = $partnerOfferService;
        $this->aboutPriyojonService = $aboutPriyojonService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function priyojonHeader()
    {
        return $this->priyojonService->headerMenu();
    }

    /**
     * @return mixed
     */
    public function priyojonOffers()
    {
       return $this->partnerOfferService->priyojonOffers();
    }

    public function partnerCampaignOffers()
    {
        return $this->partnerOfferService->campaign();
    }

    /**
     * @param Request $request
     * @param $page
     * @return mixed
     */
    public function discountOffers(Request $request, $page)
    {
       $elg = $request->status;
       $cat = $request->category;
       $area = $request->area;
       $searchStr = $request->search;
       return $this->partnerOfferService->discountOffers($page, $elg, $cat, $area, $searchStr);
    }

    public function getAboutPage($slug)
    {
        return $this->aboutPriyojonService->aboutDetails($slug);
    }

    public function offerLike($id)
    {
        return $this->partnerOfferService->offerLike($id);
    }

    public function aboutBannerImage($slug)
    {
        return $this->aboutPriyojonService->lmsAboutBanner($slug);
    }

    public function loyaltyCatOffers()
    {
        return $this->partnerOfferService->categoryOffers();
    }

    public function loyaltyTierOffers()
    {
        return $this->partnerOfferService->tierOffers();
    }

    public function aboutLoyalty()
    {
        return $this->partnerOfferService->getComponentByPageType('about_loyalty');
    }
}
