<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\BlogTags;
use App\Http\Models\BlogPosts;
use App\Http\Models\BlogComments;
use App\Http\Models\BlogPostTags;
use App\Http\Models\Area;
use App\Http\Models\BlogPostCategories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $postId = '';
        $tags = [];
        $categories = [];
        $today = date('Y-m-d H:i:s');

        foreach ($request->tags as $row) {
            BlogTags::updateOrCreate(
                ['name' => $row],
                ['created_date' => $today]
            );
            $tags[] = BlogTags::where('name', $row)->first()['id'];
        }


        $blogPosts = new BlogPosts;
        $blogPosts->pid = 0;
        $blogPosts->user_id = Auth::user()->id;
        $blogPosts->area_id = $request->area_id;
        $blogPosts->title = $request->title;
        $blogPosts->content = $request->content;
        $blogPosts->summery = '';
        $blogPosts->is_published = true;
        $blogPosts->published_date = $today;
        $blogPosts->created_date = $today;
        $blogPosts->updated_date = $today;
        if ($blogPosts->save()) {
            $postId = $blogPosts->id;
        }

        foreach ($tags as $tag) {
            $postTags[] = ['post_id' => $postId, 'tag_id' => $tag];
        }
        BlogPostTags::insert($postTags);

        foreach ($request->categories as $row) {
            $postCategories[] = ['post_id' => $postId, 'category_id' => $row];
        }
        BlogPostCategories::insert($postCategories);

        return response()->json([
            'status' => 'OK'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $lat = '';
        $lng = '';
        $orderByStr = '';
        $currArea = Area::find($id);
        if ($currArea) {
            $lat = $currArea->lat * 1;
            $lng = $currArea->lng * 1;
            $orderByData = DB::select('SELECT id, SQRT(POW((lat - ' . $lat . '), 2) + POW((lng - ' . $lng . '), 2)) AS distance FROM areas ORDER BY distance');
            $orderByStr = implode(',', array_column($orderByData, 'id'));
        }
        $pageNum = $request->pageNum * 1;
        $pageViewCount = $request->pageViewCount * 1;

        $response = [];
        $posts = BlogPosts::select('*')
            ->orderByRaw(DB::raw('FIELD(area_id, "' . $orderByStr . '") DESC'))
            ->skip(($pageNum - 1) * $pageViewCount)
            ->limit($pageViewCount)
            ->get();
        foreach ($posts as $post) {
            $post['categories'] = BlogPostCategories::where('post_id', $post->id)
                ->join('blog_categories', 'blog_post_categories.category_id', '=', 'blog_categories.id')
                ->pluck('name');

            $commentRows = BlogComments::where('post_id', $post->id)
                ->orderBy('created_date', 'desc')
                ->get()
                ->toArray();
            $post['comments'] = $this->getCommentTree($commentRows);
            $response[] = $post;
        }
        return $response;
    }

    private function getCommentTree(array &$elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['pid'] == $parentId) {
                $children = $this->getCommentTree($elements, $element['id']);
                if ($children) {
                    $element['childs'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($elements[$element['id']]);
            }
        }
        return $branch;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function like(Request $request)
    {
        $blogPost = BlogPosts::find($request->postId);
        $blogPost->like_count = $request->count;
        $blogPost->save();
        return 'OK';
    }


    public function dislike(Request $request)
    {
        $blogPost = BlogPosts::find($request->postId);
        $blogPost->dislike_count = $request->count;
        $blogPost->save();
        return 'OK';
    }

    public function leaveComment(Request $request)
    {
        $blogComments = new BlogComments;
        $blogComments->pid = $request->pid;
        $blogComments->post_id = $request->postId;
        $blogComments->user_id = Auth::user()->id;
        $blogComments->content = $request->comment;
        $blogComments->created_date = date('Y-m-d H:i:s');
        if ($blogComments->save()) {
            return 'OK';
        }
    }

    public function uploadPostImage(Request $request)
    {
        $filename = md5(date('Y-m-d H:i:s'));
        $request->file('image')->storeAs('images', $filename . '.' . $request->file('image')->extension());
        return 'dddddd';
    }
}
