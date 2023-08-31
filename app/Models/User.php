<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Authenticatable;

    protected $table = 'customers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'number_type', 'birth_date', 'profile_image', 'first_name', 'last_name', 'gender', 'alternate_phone',
        'mobile', 'address', 'district', 'thana', 'customer_account_id', 'platform', 'msisdn'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

}
