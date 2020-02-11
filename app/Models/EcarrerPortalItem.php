<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcarrerPortalItem extends Model
{
  	protected $fillable = ['ecarrer_portals_id', 'title_en', 'title_bn', 'description_en', 'description_bn', 'image', 'video', 'alt_text', 'alt_links', 'call_to_action', 'additional_info', 'is_active', 'deleted_at'];
}
