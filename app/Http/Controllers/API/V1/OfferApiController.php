<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use App\Models\Partner;
use DB;

class OfferApiController extends Controller
{
    public function getPartnerOffersData()
    {

        $data = PartnerOffer::where('is_active',1)
                            ->with(['partner'=>function($query){
                                $query->with('PartnerCategory:id,name_en,name_bn')->select();
                            }
                            ])->get();

        $data = DB::table('partner_offers as po')->where('is_active',1)
                        ->join('partners as p', 'po.partner_id', '=', 'p.id')
                        ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                        ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
                        ->get();
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
