<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;

/**
 * Class AboutUsRepository
 * @package App\Repositories
 */
class AboutUsRepository extends BaseRepository
{

    protected $model;

    /**
     * AboutUsRepository constructor.
     * @param AboutUsBanglalink $model
     */
    public function __construct(AboutUsBanglalink $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve About Banglalink
     *
     * @return mixed
     */
    public function getAboutBanglalink()
    {
        return $this->model->where('is_active',1)->get();
    }

}
