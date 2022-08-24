<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\MediaPressNewsEvent;

class MediaPressNewsEventRepository extends BaseRepository
{
    public $modelName = MediaPressNewsEvent::class;

    public function getPressNewsEvent($type, $id = null)
    {
        $data = $this->model
            ->latest()
            ->where('type', $type)
            ->select('id', 'type','title_en', 'title_bn',
                'short_details_en', 'short_details_en',
                'long_details_en', 'long_details_bn',
                'details_image', 'details_image_name_en', 'details_image_name_bn', 'details_alt_text_en', 'details_alt_text_bn',
                'thumbnail_image', 'thumbnail_image_name_en', 'thumbnail_image_name_bn', 'alt_text_en', 'alt_text_bn', 'date',
                'created_at'
            )
            ->where('status', 1);

        return ($id) ? $data->where('id', $id)->first() : $data->get();
    }

    public function filterByDate($moduleType, $from, $to)
    {
        return $this->model
            ->where('type', $moduleType)
            ->whereBetween('date', [$from, $to])
            ->select('id', 'type','title_en', 'title_bn',
                'short_details_en', 'short_details_en',
                'long_details_en', 'long_details_bn',
                'details_image', 'details_alt_text_en',
                'thumbnail_image', 'alt_text_en','date'
            )
            ->where('status', 1)
            ->get();
    }
}
