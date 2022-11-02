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

        return [ 'banner' => ($result[0] ?? ''), 'text' => ($result[1] ?? '')];
    }
}
