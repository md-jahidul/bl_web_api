<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeLog extends Model
{
    protected $fillable = [
        'trx_id',
        'msisdns',
        'requester_msisdn',
        'recharge_amounts',
        'cash_back_amounts',
        'total_payment_amount',
        'initiate_status',
        'initiate_status_code',
        'execute_status',
        'execute_status_code',
        'excitation_remarks',
        'gateway',
        'channel'
    ];
}
