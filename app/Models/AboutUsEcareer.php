<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUsEcareer extends Model
{

    /**
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aboutUsEcareerItems()
    {
        return $this->hasMany(AboutUsEcareerItem::class, 'about_us_ecareers_id');
    }


}
