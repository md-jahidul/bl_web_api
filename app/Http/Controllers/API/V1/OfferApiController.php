<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use App\Models\Partner;

class OfferApiController extends Controller
{
    public function getPartnerOffersData()
    {

        $data = PartnerOffer::where('is_active',1)
                            ->with(['partner'=>function($query){
                                $query->with('PartnerCategory:id,name_en,name_bn')->select();
                            }
                            ])->get();
        return $data;
    }

    public function index()
    {
        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message' => 'Data Found!',
                'data' => $this->getPartnerOffersData()
            ]
        );
    }
}
