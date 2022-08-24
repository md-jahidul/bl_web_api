<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpInitiativeTabComponent extends Model
{
    protected $casts = [
        'multiple_attributes' => 'array'
    ];

    public function multiComponent()
    {
        return $this->hasMany(CorpIntComponentMultiItem::class, 'corp_int_tab_com_id', 'id');
    }

    public function batchTab()
    {
        return $this->hasMany(CorpIntBatchComponentTab::class, 'corp_int_tab_com_id', 'id');
    }
}
