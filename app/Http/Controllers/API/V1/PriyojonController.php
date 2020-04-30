<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerOfferResource;
use App\Models\Priyojon;
use App\Services\AboutPageService;
use App\Services\PartnerOfferService;
use Illuminate\Database\QueryException;
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
     * PriyojonController constructor.
     * @param PartnerOfferService $partnerOfferService
     * @param AboutPageService $aboutPriyojonService
     */
    public function __construct(
        PartnerOfferService $partnerOfferService,
        AboutPageService $aboutPriyojonService
    ) {
        $this->partnerOfferService = $partnerOfferService;
        $this->aboutPriyojonService = $aboutPriyojonService;
    }


    /**
     * @return mixed
     */
    public function PriyojonHeader()
    {
        try{
            $priyojonHeader = Priyojon::where('parent_id', 0)->with('children')->get();

            if (isset($priyojonHeader)) {

                return response()->success($priyojonHeader, 'Data Found!');
            }

            return response()->error('Data Not Found!');

        }catch (QueryException $e) {
            return response()->error('Something wrong!', $e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function priyojonOffers()
    {
       return $this->partnerOfferService->priyojonOffers();
    }

    /**
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
}
