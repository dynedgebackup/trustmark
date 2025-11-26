<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'brgy_code',
        'reg_no',
        'reg_region',
        'prov_no',
        'prov_desc',
        'mun_no',
        'mun_desc',
        'brgy_name',
        'brgy_description',
        'brgy_area_code',
        'brgy_office',
        'dist_code',
        'brgy_display_for_bplo',
        'brgy_display_for_rpt',
        'brgy_display_for_rpt_locgroup',
        'uacs_code',
        'psg_code',
        'psa_code',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
