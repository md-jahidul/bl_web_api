<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BlLabUser extends Authenticatable implements JWTSubject
{
//    use HasFactory;
//    use Notifiable;
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = Hash::make($value);
    }

    // Rest omitted for brevity

    public function getJWTIdentifier () {
        return $this->getKey();
    }

    public function getJWTCustomClaims () {
        return [];
    }
}
