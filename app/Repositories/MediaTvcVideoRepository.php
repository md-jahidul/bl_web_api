<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\MediaTvcVideo;

class MediaTvcVideoRepository extends BaseRepository
{
    public $modelName = MediaTvcVideo::class;

    public function getVideoItems($id = null)
    {
        $data = $this->model->where('status', 1)
            ->select('id', 'title_en', 'title_bn', 'video_url');

        return ($id) ? $data->where('id', $id)->first() : $data->get();
    }
}
