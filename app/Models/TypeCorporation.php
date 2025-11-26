<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeCorporation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
