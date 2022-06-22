<?php

namespace App\Http\Controllers;

use App\Http\Models\Location;
use App\Http\Models\Job;

class LocationController extends Controller
{
    public function show(Location $location)
    {
        $jobs = Job::with('company')
            ->whereHas('location', function($query) use($location) {
                $query->whereId($location->id);
            })
            ->paginate(7);

        $banner = 'Location: '.$location->name;
    
        return view('jobs.index', compact(['jobs', 'banner']));
    }
}
