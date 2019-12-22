<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfigResource
{
    /**
     * @param $data
     * @return array
     */
    public function data($data)
    {
        return [
            "site_logo" => ($data['site_logo'] != '') ? env("IMAGE_HOST_URL") . $data['site_logo'] : null,
            "logo_alt_text" => $data['logo_alt_text'] ?? null,
        ];
    }
}
