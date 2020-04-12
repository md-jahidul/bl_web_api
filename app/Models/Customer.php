<?php

namespace App\Models;

use App\Services\Banglalink\CustomerPackageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'email', 'phone','customer_account_id','birth_date','profile_image',
    ];

    /**
     * @return BelongsToMany
     */
    public function shortcuts()
    {
        return $this->belongsToMany(
            Shortcut::class,
            'shortcut_user',
            'user_id',
            'shortcut_id'
        );
    }

    public static function package(Customer $customer)
    {
        $package_service = new CustomerPackageService();

        return $package_service->getPackageInfo($customer->customer_account_id);
    }

}
