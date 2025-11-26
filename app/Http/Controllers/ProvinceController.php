<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function getProvinces($reg_no)
    {
        $provinces = Province::where('reg_no', $reg_no)
                        ->where('is_active', 1)
                        ->pluck('prov_desc', 'id');

        return response()->json($provinces);
    }
}
