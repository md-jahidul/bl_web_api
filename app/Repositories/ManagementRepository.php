<?php

namespace App\Repositories;

use App\Models\AboutUsManagement;

/**
 * Class AboutUsRepository
 * @package App\Repositories
 */
class ManagementRepository extends BaseRepository
{

    protected $model;


    /**
     * ManagementRepository constructor.
     * @param AboutUsManagement $model
     */
    public function __construct(AboutUsManagement $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve Management info
     *
     * @return mixed
     */
    public function getAboutManagement()
    {
        return $this->model->where('is_active', 1)->orderBy('display_order', 'asc')->get();
    }

}
