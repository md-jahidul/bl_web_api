<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerOfferResource;
use App\Models\Priyojon;
use App\Services\PartnerOfferService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PriyojonController extends Controller
{

    /**
     * @var PartnerOfferService
     */
    private $partnerOfferService;

    /**
     * PriyojonController constructor.
     * @param PartnerOfferService $partnerOfferService
     */
    public function __construct(
        PartnerOfferService $partnerOfferService
    ) {
        $this->partnerOfferService = $partnerOfferService;
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
}
