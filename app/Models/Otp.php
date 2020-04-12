<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Otp extends Authenticatable
{
    use Notifiable;

    /**
     * The otps table.
     *
     * @var string
     */
    protected $table = 'otps';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'phone', 'session_id', 'token','starts_at','expires_at'
    ];
}
