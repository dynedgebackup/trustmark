<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prov_code',
        'reg_no',
        'prov_no',
        'prov_desc',
        'uacs_code',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
