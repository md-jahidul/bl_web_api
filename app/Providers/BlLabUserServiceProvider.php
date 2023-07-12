<?php

namespace App\Providers;

use App\Models\BlLabUser;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class BlLabUserServiceProvider implements UserProvider
{
    public function retrieveByToken ($identifier, $token) {
        throw new Exception('Method not implemented.');
    }

    public function updateRememberToken (Authenticatable $user, $token) {
        throw new Exception('Method not implemented.');
    }

    public function retrieveById ($identifier) {
        return BlLabUser::find($identifier);
    }

    public function retrieveByCredentials (array $credentials) {
        return BlLabUser::where('email', $credentials['email'])->first();
    }

    public function validateCredentials (Authenticatable $user, array $credentials) {
        return $credentials['password'];
    }
}
