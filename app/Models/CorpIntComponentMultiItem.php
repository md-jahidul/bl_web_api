<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpIntComponentMultiItem extends Model
{
    protected $fillable = [
        'corp_int_tab_com_id', 'batch_com_id',
        'title_en', 'title_bn', 'details_en', 'details_bn',
        'base_image', 'image_name_en', 'image_name_bn',
        'alt_text_en', 'alt_text_bn'
    ];
}
