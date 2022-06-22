<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Category;
use App\Http\Requests\MassDestroyCategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = [];
        $parentCategories = Category::where('pid', '0')->get();
        foreach ($parentCategories as $parentCategory) {
            $parentCategory['parent'] = '';
            $categories[] = $parentCategory;
            $childrens = Category::where('pid', $parentCategory->id)->get();
            foreach ($childrens as $children) {
                $children['parent'] = $parentCategory->name;
                $categories[] = $children;
            }
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parentCategories = Category::where('pid', '0')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->all());

        return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category)
    {
        abort_if(Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $parentCategories = Category::where('pid', '0')->where('id', '!=', $category->id)->get();
        $parentCategory = Category::find($category->id);
        return view('admin.categories.edit', compact(['category', 'parentCategories', 'parentCategory']));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->all());

        return redirect()->route('admin.categories.index');
    }

    public function show(Category $category)
    {
        abort_if(Gate::denies('category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.categories.show', compact('category'));
    }

    public function destroy(Category $category)
    {
        abort_if(Gate::denies('category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Category::find($category->id)->delete();
        Category::where('pid', $category->id)->delete();
        // $category->delete();

        return back();
    }

    public function massDestroy(MassDestroyCategoryRequest $request)
    {
        Category::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getCategoriesByParent(Request $request) 
    {
        if($request->pid !== 'all') {
            return Category::where('pid', $request->pid)->select('id', 'name')->get();
        } else {
            return Category::where('pid', '!=', '0')->select('id', 'name')->get();
        }
    }
}
