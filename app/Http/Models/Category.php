<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public $table = 'categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'pid',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }
}
