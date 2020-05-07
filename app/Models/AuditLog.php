<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AuditLog
 * @package App\Models
 */
class AuditLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msisdn',
        'source',
        'browse_url',
        'user_ip',
        'browser_info',
        'device_id',
        'status_code'
    ];
}
