<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutCms extends Model
{
    protected $table = 'about_cms';
    protected $fillable = [
        'user_id',
        'description',
        'number_of_project',
        'programming_language_known',
        'framework_known',
        'client_handled',
    ];
}
