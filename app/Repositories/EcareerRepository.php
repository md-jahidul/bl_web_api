<?php

namespace App\Repositories;

use App\Models\EcarrerPortal;

/**
 * Class EcareerRepository
 * @package App\Repositories
 */
class EcareerRepository extends BaseRepository
{

    protected $model;


    /**
     * EcareerRepository constructor.
     * @param EcarrerPortal $model
     */
    public function __construct(EcarrerPortal $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve eCareerInfo
     *
     * @return mixed
     */
    public function getEcareersInfo()
    {
        return $this->model->with('portalItems')->get();
    }

}
