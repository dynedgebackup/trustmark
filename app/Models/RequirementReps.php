<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementReps extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'description',
        'status',
        'with_expiration',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
