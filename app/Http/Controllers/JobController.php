<?php

namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\Job;
use App\Http\Models\Location;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('company')
            ->paginate(7);

        $banner = 'Jobs';

        return view('jobs.index', compact(['jobs', 'banner']));
    }

    public function show(Job $job)
    {
        $job->load('company');

        return view('jobs.show', compact('job'));
    }
}
