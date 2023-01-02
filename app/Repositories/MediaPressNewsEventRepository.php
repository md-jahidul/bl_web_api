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
                'details_image', 'details_alt_text_en',
                'thumbnail_image', 'alt_text_en','date',
                'created_at'
            )
            ->where('status', 1);

        return ($id) ? $data->where('id', $id)->first() : $data->get();
    }

    public function landingDataByRefType($postRefType, $id = [])
    {
        return $this->model
            ->latest()
            ->where('reference_type', $postRefType)
            ->select('title_en', 'title_bn',
                'short_details_en', 'short_details_bn',
                'long_details_en', 'long_details_bn',
                'details_image', 'details_alt_text_en',
                'thumbnail_image', 'alt_text_en','date',
                'read_time'
            )
            ->where('status', 1)
            ->whereIn('id', $id)
            ->get();
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
