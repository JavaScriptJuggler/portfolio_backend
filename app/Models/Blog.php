<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';
    protected $fillable = [
        'blog_name',
        'short_description',
        'blog_content',
        'slug',
        'published_at',
        'thumbnel',
    ];
}
