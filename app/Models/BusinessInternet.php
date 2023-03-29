<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInternet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_internet_packages';

    public function tag_category()
    {
        return $this->belongsTo(TagCategory::class);
    }
}