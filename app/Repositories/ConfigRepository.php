<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\Config;
use App\Models\Menu;
use App\Models\Prize;

class ConfigRepository extends BaseRepository
{
    public $modelName = Config::class;

    public function headerSettings()
    {
        return $this->model->where('key', 'site_logo')
            ->orWhere('key', 'logo_alt_text')
            ->get();
    }

    public function resourceData($data)
    {
        return [
            "site_logo" => ($data['site_logo'] != '') ? env("IMAGE_HOST_URL") . $data['site_logo'] : null,
            "logo_alt_text" => $data['logo_alt_text'] ?? null,
        ];
    }

    public function whereNotIn(){
        return $this->model->whereNotIn('key', ['site_logo', 'logo_alt_text'])
            ->get();
    }
}
