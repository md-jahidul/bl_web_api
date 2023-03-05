<?php

namespace App\Models;

use App\Traits\LogModelAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlCashBack extends Model
{

    public function scopeStartEndDate($query)
    {
        $bdTimeZone = Carbon::now('Asia/Dhaka');
        $dateTime = $bdTimeZone->toDateTimeString();

        return $query->where(function ($query) use ($dateTime) {
            $query->where('start_date', '<=', $dateTime)
                ->orWhereNull('start_date');
        })
        ->where(function ($query) use ($dateTime) {
            $query->where('end_date', '>=', $dateTime)
                ->orWhereNull('end_date');
        });
    }

    public function cashBackProducts(): HasMany
    {
        return $this->hasMany(AlCashBackProduct::class, 'al_cash_back_id', 'id');
    }
}
