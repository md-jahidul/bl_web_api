<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


class DistrictThanaService extends ApiBaseService
{

    /**
     * @param $slug
     * @return mixed
     */
    public function district()
    {
        $district = DB::table('districts')->get();
        return $this->sendSuccessResponse($district, 'District list');
    }

    public function thana($districtId)
    {
        $thana = DB::table('thanas')->where('district_id', $districtId)->get();
        return $this->sendSuccessResponse($thana, 'Thana list');
    }
}
