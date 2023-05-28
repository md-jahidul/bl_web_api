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
        return $this->belongsTo(AlCoreProduct::class, 'product_code', 'product_code');
    }

    public function sim_category()
    {
        return $this->belongsTo(SimCategory::class);
    }

    public function offer_category()
    {
        return $this->belongsTo(OfferCategory::class);
    }


    public function tag()
    {
        return $this->belongsTo(TagCategory::class, 'tag_category_id', 'id')
            ->select('id', 'name_en as tag_name_en', 'name_bn as tag_name_bn');
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
                'sms_rate_unit',
                'minute_volume',
                'call_rate as callrate_offer',
                'call_rate_unit',
                'sms_rate as sms_rate_offer',
                'renew_product_code',
                'recharge_product_code',
                'sd_vat_tax_en',
                'sd_vat_tax_bn'
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
        $fKey = isset($this->offer_info['other_offer_type_id']) ? $this->offer_info['other_offer_type_id'] : 0;
        return $this->hasMany(OtherRelatedProduct::class, $fKey);
    }
}
