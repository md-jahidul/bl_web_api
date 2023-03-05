<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlCashBackProduct extends Model
{
    public function campaign()
    {
        return $this->belongsTo(AlCashBack::class, 'al_cash_back_id', 'id');
    }
}
