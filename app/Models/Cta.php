<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cta extends Model
{
    protected $table = "cta";
    protected $fillable = [
        'heading',
        'description',
        'user_id',
    ];
}
