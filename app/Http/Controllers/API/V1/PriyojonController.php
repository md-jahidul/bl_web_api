<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Priyojon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

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
}
