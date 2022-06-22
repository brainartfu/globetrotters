<?php

namespace App\Http\Controllers;

use App\Http\Models\Area;
use App\Http\Models\BlogTags;
use App\Http\Models\Category;
use App\Http\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index($lang = 'en', $any = null, $category = 'category', $post = 'post')
    {
        $areaId = 0;
        $initLat = 51.51316578087706;
        $initLng = 0.2629141959194836;
        $initZoom = 4;

        $blogTags = BlogTags::all();
        $categories = [];
        $pCategories = Category::where('pid', '0')->get();
        if (sizeof($pCategories)) {
            foreach ($pCategories as $pCategory) {
                $cCategories = Category::where('pid', $pCategory->id)->get();
                $subCategoryNames = [];
                if (sizeof($cCategories)) {
                    foreach ($cCategories as $cCategory) {
                        $subCategoryNames[] = $cCategory->name;
                    }
                }
                $pCategory->sub_categories = implode(',', $subCategoryNames);
                $categories[] = $pCategory;
            }
        }

        $area = Area::where('url', $any)->first();
        if ($area) {
            $areaId = $area->id;
            $initLat = $area->lat;
            $initLng = $area->lng;
            $initZoom = $area->zoom;
        }

        return view('index', compact([
            'initLat',
            'initLng',
            'initZoom',
            'areaId',
            'categories',
            'blogTags',
        ]));
    }

    public function search(Request $request)
    {
        $jobs = Job::with('company')
            ->searchResults()
            ->paginate(7);

        $banner = 'Search results';

        return view('jobs.index', compact(['jobs', 'banner']));
    }
}
