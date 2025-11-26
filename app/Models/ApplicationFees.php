<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationFees extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'app_code',
        'app_name',
        'fee_id',
        'fee_name',
        'amount',
        'created_by',
        'create_date',
        'modified_by',
        'modified_date',
        'exclude_due_to_bmbe',
        'is_application_fee'
    ];
}
