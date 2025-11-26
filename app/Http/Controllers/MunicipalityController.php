<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    public function getMunicipalities($prov_no)
    {
        $municipalities = Municipality::where('prov_no', $prov_no)
                        ->where('is_active', 1)
                        ->pluck('mun_desc', 'id');

        return response()->json($municipalities);
    }
}
