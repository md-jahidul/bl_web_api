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

    public function getDataBySlug($slug)
    {
        return $this->model
            ->where('status', 1)
            ->where('url_slug_en', $slug)
            ->orWhere('url_slug_bn', $slug)
            ->select('id')
            ->first();
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
                'read_time', 'details_btn_en', 'details_btn_bn',
                'tag_en', 'tag_bn', 'url_slug_en', 'url_slug_bn'
            )
            ->where('status', 1)
            ->whereIn('id', $id)
            ->get();
    }

    public function filterArchive($postRefType, $param,$limit)
    {
        $q = $this->model
            ->latest()
            ->select('title_en', 'title_bn',
                'short_details_en', 'short_details_bn',
                'long_details_en', 'long_details_bn',
                'details_image', 'details_alt_text_en',
                'thumbnail_image', 'alt_text_en','date',
                'read_time', 'details_btn_en', 'details_btn_bn',
                'tag_en', 'tag_bn', 'url_slug_en', 'url_slug_bn'
            )
            ->where('reference_type', $postRefType);
            if(!empty($param['media_news_category_id'])){
                $q->where('media_news_category_id', $param['media_news_category_id']);
            }
            if(!empty($param['year']) && empty($param['month'])){
                $q->whereRaw('DATE_FORMAT(date,"%Y") ='.$param['year']);
            }
            if(!empty($param['year']) && !empty($param['month'])){
                $q->whereRaw("DATE_FORMAT(date,'%Y-%m') = '".$param['year']."-".$param['month']."'");
            }
        $data = $q->where('status', 1)
        ->paginate($limit);
        return $data;
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
