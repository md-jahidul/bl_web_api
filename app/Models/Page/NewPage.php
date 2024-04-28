<?php

namespace App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page\NewPageComponent;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewPage extends Model
{
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];

    public function pageComponents(): HasMany
    {
        return $this->hasMany(NewPageComponent::class, 'page_id', 'id')
            ->select('id', 'name', 'type', 'attribute','config')
            ->where('status', 1)
            ->orderBy('order', 'asc');
    }
}
