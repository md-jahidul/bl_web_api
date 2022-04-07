<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyBlProduct extends Model
{
    protected $guarded = ['id'];

    public function details()
    {
        return $this->belongsTo(ProductCore::class, 'product_code', 'product_code');
    }

    public function tags()
    {
        return $this->hasManyThrough(
            ProductTag::class,
            MyBlProductTag::class,
            'product_code',
            'id',
            'product_code',
            'product_tag_id'
        );
    }

    /**
     * Returns schedule status according to current time
     * 1 = Active schedule, 2 = To be hidden, 3 = Completed Schedule, 4 = To be shown
     * @return bool|int
     */
    public function scheduleStatus()
    {
        $showFrom = $this->show_from ? strtotime($this->show_from) : 0;
        $hideFrom = $this->hide_from ? strtotime($this->hide_from) : 0;
        $currentTime = strtotime(date('Y-m-d H:i:s'));

        if ($showFrom && $hideFrom) {
            return (($currentTime >= $showFrom) && ($currentTime <= $hideFrom)) ? 1 : (($currentTime > $hideFrom) ? 3 : 4);
        } elseif ($showFrom) {
            return ($showFrom > $currentTime) ? 4 : 1;
        } elseif ($hideFrom) {
            return ($hideFrom > $currentTime) ? 2 : 3;
        } else {
            return false;
        }
    }
}
