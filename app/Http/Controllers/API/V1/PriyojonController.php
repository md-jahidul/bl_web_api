<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Priyojon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriyojonController extends Controller
{
    public function PriyojonHeader()
    {
        try{
            $priyojonHeader = Priyojon::where('parent_id', 0)->with('children')->get();

            if (isset($priyojonHeader)) {

                return response()->success($priyojonHeader, 'Data Found!');
            }

            return response()->error('Data Not Found!');

        }catch (QueryException $e) {
            return response()->error('Data Not Found!', $e->getMessage());
        }
    }

    public function priyojonOffers()
    {
        try{
            $priyojonOffers = DB::table('partner_offers as po')
                ->where('po.is_active',1)
                ->join('partners as p', 'po.partner_id', '=', 'p.id')
                ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
                ->orderBy('po.display_order')
                ->get();


            return $priyojonOffers;

            if (isset($priyojonOffers)) {
                return response()->success($priyojonOffers, 'Data Found!');
            }

            return response()->error('Data Not Found!');

        }catch (QueryException $e) {
            return response()->error('Data Not Found!', $e->getMessage());
        }
    }
}
