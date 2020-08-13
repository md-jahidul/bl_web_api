<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FourGDevice extends Model
{
    protected $hidden = ['status', 'created_at', 'updated_at'];

    protected $casts = [
        'device_tags' => 'array'
    ];

    public function deviceTags()
    {
        return $this->belongsToMany(FourGDeviceTag::class, 'four_g_device_tag_device', 'device_id', 'tag_id');
    }
}
