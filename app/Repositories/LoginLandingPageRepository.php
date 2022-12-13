<?php

/**
 * Created by VS.
 * User: BS23
 * Date: 02-NOV-2022
 * Time: 3:00 PM
 */

namespace App\Repositories;

use App\Models\Config;

class LoginLandingPageRepository extends BaseRepository
{
    public $modelName = Config::class;

    public function findLoginPageBanner()
    {
        $result = $this->model->where('key', 'login_page_banner')
                ->orWhere('key', 'login_page_banner_alt_text')->get();

        $footer_settings = [];
        foreach ($result as $settings) {
            $footer_settings[$settings->key] = $settings->value;
        }
        return $footer_settings;
    }
}
