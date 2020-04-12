<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AppServiceProduct extends Model
{
    protected $guarded = ['id'];

    public function appServiceTab()
    {
        return $this->belongsTo(AppServiceTab::class, 'app_service_tab_id', 'id');
    }

    public function appServiceCat()
    {
        return $this->belongsTo(AppServiceCategory::class, 'app_service_cat_id', 'id');
    }

    public function scopeCheckStartEndDate($query)
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
