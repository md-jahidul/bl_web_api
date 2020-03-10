<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
   protected $fillable = [
   	'section_details_id',
    'page_type',
    'title_en',
    'title_bn',
    'slug',
    'description_en',
    'description_bn',
    'editor_en',
    'editor_bn',
    'image',
    'alt_text',
    'video',
    'alt_links',
    'component_type',
    'component_order',
    'multiple_attributes',
    'status',
    'other_attributes',
    'deleted_at'
 	];

    protected $casts = [
        'other_attributes' => 'array',
        'multiple_attributes' => 'array'
    ];

    public function productInfo()
    {
        return $this->hasOne(Product::class,  'id','offer_type_id')->productCore();
    }
}
