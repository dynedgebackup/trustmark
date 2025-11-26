<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BusinessFollowups extends Model
{
    use HasFactory;

    protected $fillable = [
        'busn_id',
        'year',
        'is_type',
        'followup_date',
        'followup_message'
    ];
}
