<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\MediaBannerImage;


class MediaBannerImageRepository extends BaseRepository
{
    public $modelName = MediaBannerImage::class;

    public function bannerImage($moduleType)
    {
        return $this->model->where('module_type', $moduleType)
            ->select('id', 'module_type', 'banner_image_url',
                'banner_mobile_view', 'alt_text_en', 'page_header', 'page_header_bn', 'schema_markup')
            ->first();
    }
}
