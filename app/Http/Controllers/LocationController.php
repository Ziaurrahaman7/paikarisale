<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use lemonpatwari\bangladeshgeocode\Models\Division;
use lemonpatwari\bangladeshgeocode\Models\District;
use lemonpatwari\bangladeshgeocode\Models\Thana;

class LocationController extends Controller
{
    public function getDistricts($division_id)
    {
        $districts = District::where('division_id', $division_id)->get();
        return response()->json($districts);
    }

    public function getUpazilas($district_id)
    {
        $upazilas = Thana::where('district_id', $district_id)->get();
        return response()->json($upazilas);
    }
}
