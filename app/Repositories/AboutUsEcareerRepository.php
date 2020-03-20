<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\AboutUsEcareer;

class AboutUsEcareerRepository extends BaseRepository
{
    public $modelName = AboutUsEcareer::class;



    /**
     * Retrieve eCareerInfo
     *
     * @return mixed
     */
    public function getEcareersInfo()
    {
        return $this->model->with('aboutUsEcareerItems')->get();

        /*return $this->model->with(['aboutUsEcareerItems' => function ($query) {
            $query->whereNull('deleted_at');
        }])->where('category', 'life_at_bl_diversity')->first();*/
    }


}
