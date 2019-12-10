<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\SimCategory;

class Product extends Model
{
    protected $casts = [
        'offer_info' => 'array'
    ];

    public function productCore()
    {
        return $this->belongsTo(ProductCore::class, 'product_code', 'product_code');
    }

    public function sim_category()
    {
        return $this->belongsTo(SimCategory::class);
    }


    /**
     * @param $query
     * @return mixed
     */
    public function scopeProductCore($query)
    {
        return $query->with(['productCore' => function ($q) {
            $q->select(
                'product_code',
                'activation_ussd as ussd_en',
                'balance_check_ussd',
                'price',
                'vat',
                'mrp_price as price_tk',
                'validity as validity_days',
                'validity_unit',
                'internet_volume_mb',
                'sms_volume',
                'minute_volume',
                'call_rate',
                'sms_rate'
            );
        }]);
    }

    public function scopeCategory($query, $type)
    {
        return $query->whereHas('sim_category', function ($q) use ($type) {
            $q->where('alias', $type);
        });
    }

    /**
     * @param $query
     * @return mixed
     */
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

    public function product_details()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function related_product()
    {
        return $this->hasMany(RelatedProduct::class);
    }

    public function other_related_product()
    {
        return $this->hasMany(OtherRelatedProduct::class, $this->offer_info['other_offer_type_id']);
    }
}
