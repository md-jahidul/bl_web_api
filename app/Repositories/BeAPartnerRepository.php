<?php

namespace App\Repositories;

use App\Models\BeAPartner;

class BeAPartnerRepository extends BaseRepository
{
    public $modelName = BeAPartner::class;

    public function getOneData()
    {
        return $this->model->first();
    }
}
