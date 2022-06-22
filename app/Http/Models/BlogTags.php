<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTags extends Model
{
    protected $table = 'blog_tags';
    protected $fillable = ['name', 'created_date'];
    public $timestamps = false;
}
