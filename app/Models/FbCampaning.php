<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbCampaning extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fb_registrations';

    protected $fillable = [
        'name',
        'district',
        'thana',
        'phone',
        'al_phone',
        'email',
        'purpose'
    ];
}