<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'business_id',
        'transaction_id',
        'or_serial_number',
        'or_number',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'date',
        'response_date',
        'payment_in_process',
        'skey',
        'cardholder',
        'tranID',
        'txnstatus',
        'total_paid_amount',
        'channel_id',
        'created_at',
        'updated_at',
    ];
}
