<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DistrictThanaController extends Controller
{
    public function district()
    {
       $district = DB::table('districts')->get();
       return response()->json($district);
    }

    public function thana($districtId)
    {
        $district = DB::table('thanas')->where('district_id', $districtId)->get();
        return response()->json($district);
    }
}
