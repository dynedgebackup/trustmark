<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'mun_code',
        'reg_no',
        'prov_no',
        'mun_no',
        'mun_desc',
        'mun_complete_desc',
        'mun_zip_code',
        'mun_area_code',
        'mun_display_for_bplo',
        'mun_display_for_rpt',
        'mun_display_for_welfare',
        'mun_display_for_accounting',
        'mun_display_for_finance',
        'mun_display_for_economic',
        'mun_display_for_cpdo',
        'mun_display_for_eng',
        'mun_display_for_occupancy',
        'mun_display_for_agriculture',
        'uacs_code',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
