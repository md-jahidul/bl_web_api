<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpIntBatchComponentTab extends Model
{
    protected $fillable = ['corp_int_tab_com_id', 'title_en', 'title_bn'];

    public function batchTabComponents()
    {
        return $this->hasMany(CorpIntComponentMultiItem::class, 'batch_com_id', 'id');
    }
}
