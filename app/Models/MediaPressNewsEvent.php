<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPressNewsEvent extends Model
{
    protected $guarded = ['id'];

    function mediaNewsCategory()
    {
        return $this->belongsTo(MediaNewsCategory::class, 'media_news_category_id');
    }

}
