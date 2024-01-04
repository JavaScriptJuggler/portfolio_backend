<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'hero';
    protected $fillable = [
        'name',
        'sub_title',
        "resume_link",
        "hero_image_link",
        'user_id',
    ];
}
