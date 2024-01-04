<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeatures extends Model
{
    protected $table = 'service_features';
    protected $fillable = [
        'user_id',
        'heading',
        'description',
        'icon_link',
    ];
}
