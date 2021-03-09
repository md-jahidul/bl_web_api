<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlCsrfToken extends Model
{
    protected $fillable = ['token', 'starts_at', 'expires_at'];
}
