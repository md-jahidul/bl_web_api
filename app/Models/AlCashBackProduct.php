<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AlCashBackProduct extends Model
{
    public function campaign()
    {
        return $this->belongsTo(AlCashBack::class, 'al_cash_back_id', 'id');
    }

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
}
