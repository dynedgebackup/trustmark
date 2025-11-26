<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFees extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'tax_year',
        'busn_id',
        'app_code',
        'app_name',
        'fee_id',
        'fee_name',
        'amount',
        'category_id',
        'payment_id',
        'created_by',
        'create_date'
    ];

    public $timestamps = false;
}
