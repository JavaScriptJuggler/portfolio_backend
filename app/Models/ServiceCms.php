<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCms extends Model
{
    protected $table = 'service_cms';
    protected $fillable = [
        'user_id',
        'heading',
        'description',
    ];
}
