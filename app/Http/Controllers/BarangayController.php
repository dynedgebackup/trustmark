<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangayController extends Controller
{
    public function getBarangays($region_id, $province_id, $municipality_id)
    {
        $barangays = DB::table('barangays AS bgf')
            ->where('bgf.is_active', 1)
            ->where('bgf.reg_no', $region_id)
            ->where('bgf.prov_no', $province_id)
            ->where('bgf.mun_no', $municipality_id)
            ->pluck('bgf.brgy_name', 'bgf.id');

        return response()->json($barangays);
    }
}
