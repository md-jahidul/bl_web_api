<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadRequest extends Model
{
    protected $fillable = ['name', 'company_name', 'mobile', 'email', 'district', 'thana', 'address', 'quantity', 'package', 'request_type'];
}
