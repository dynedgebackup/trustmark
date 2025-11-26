<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'is_active',
        'is_others',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
