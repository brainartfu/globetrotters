<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Area;

class AreaController extends Controller
{
    public function store(Request $request)
    {
        $url = $request->url;
        $isExist = Area::where('url', $url)->count() > 0;
        $area = new Area;
        if ($isExist) {
            if (Area::where('url', $url)->update([
                'country' => $request->country === NULL ? '' : $request->country,
                'administrative_area_level_1' => $request->administrative_area_level_1 === NULL ? '' : $request->administrative_area_level_1,
                'administrative_area_level_2' => $request->administrative_area_level_2 === NULL ? '' : $request->administrative_area_level_2,
                'administrative_area_level_3' => $request->administrative_area_level_3 === NULL ? '' : $request->administrative_area_level_3,
                'administrative_area_level_4' => $request->administrative_area_level_4 === NULL ? '' : $request->administrative_area_level_4,
                'administrative_area_level_5' => $request->administrative_area_level_5 === NULL ? '' : $request->administrative_area_level_5,
                'locality' => $request->locality === NULL ? '' : $request->locality,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'zoom' => $request->zoom,
                'formatted_address' => $request->formatted_address,
            ])) {
                $response = Area::where('url', $url)->first()['id'];
            }
        } else {
            $area->country = $request->country === NULL ? '' : $request->country;
            $area->administrative_area_level_1 = $request->administrative_area_level_1 === NULL ? '' : $request->administrative_area_level_1;
            $area->administrative_area_level_2 = $request->administrative_area_level_2 === NULL ? '' : $request->administrative_area_level_2;
            $area->administrative_area_level_3 = $request->administrative_area_level_3 === NULL ? '' : $request->administrative_area_level_3;
            $area->administrative_area_level_4 = $request->administrative_area_level_4 === NULL ? '' : $request->administrative_area_level_4;
            $area->administrative_area_level_5 = $request->administrative_area_level_5 === NULL ? '' : $request->administrative_area_level_5;
            $area->locality = $request->locality === NULL ? '' : $request->locality;
            $area->lat = $request->lat;
            $area->lng = $request->lng;
            $area->zoom = $request->zoom;
            $area->formatted_address = $request->formatted_address;
            $area->url = $request->url;
            if ($area->save()) {
                $response = $area->id;
            }
        }
        return response()->json([
            'response' => $response
        ]);
    }
}
