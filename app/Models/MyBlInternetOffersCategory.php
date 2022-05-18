<?php

namespace App\Models;

// use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class MyBlInternetOffersCategory extends Model
{
    // use Sluggable;

    protected $guarded = ['id'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    // public function sluggable()
    // {
    //     return [
    //         'slug' => [
    //             'source' => 'name'
    //         ]
    //     ];
    // }

    public function productCodes()
    {
        return $this->hasMany(MyBlProductTab::class);
    }
}
