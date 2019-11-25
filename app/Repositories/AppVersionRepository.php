<?php

namespace App\Repositories;

use App\Models\AppVersion;

/**
 * Class AppVersionRepository
 * @package App\Repositories
 */
class AppVersionRepository extends BaseRepository
{
    /**
     * @var Banner
     */
    protected $model;

    /**
     * AppVersionRepository constructor.
     * @param AppVersion $model
     */
    public function __construct(AppVersion $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve Banner info
     *
     * @return mixed
     */
    public function getAppVersion()
    {
        return $this->model->get();
    }

    /**
     * @param $platform
     * @return mixed
     */
    public function getAppVersionWithPlatform($platform)
    {
        return $this->model->where('platform', $platform)->get();
    }
}
