<?php

namespace App\Models;



use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class BlLabUser extends Model implements JWTSubject, Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    // Rest omitted for brevity

    public function getJWTIdentifier () {
        return $this->getKey();
    }

    public function getJWTCustomClaims () {
        return [];
    }

    public function getAuthIdentifierName () {
        // TODO: Implement getAuthIdentifierName() method.
    }

    public function getAuthIdentifier () {
        // TODO: Implement getAuthIdentifier() method.
    }

    public function getAuthPassword () {
        // TODO: Implement getAuthPassword() method.
    }

    public function getRememberToken () {
        // TODO: Implement getRememberToken() method.
    }

    public function setRememberToken ($value) {
        // TODO: Implement setRememberToken() method.
    }

    public function getRememberTokenName () {
        // TODO: Implement getRememberTokenName() method.
    }
}
