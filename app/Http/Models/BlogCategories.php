<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategories extends Model
{
    protected $table = 'blog_categories';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'created_date'
    ];
}
