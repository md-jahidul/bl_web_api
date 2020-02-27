<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcareerPortal extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ecarrer_portals';

   protected $fillable = ['title_en', 'title_bn', 'slug', 'description_en', 'description_bn', 'image', 'video', 'alt_text', 'category', 'route_slug', 'category_type', 'additional_info', 'is_active', 'has_items', 'deleted_at'];


   public function portalItems(){

   	return $this->hasMany(EcarrerPortalItem::class, 'ecarrer_portals_id');

   }
}
