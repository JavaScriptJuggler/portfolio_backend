<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioCategory extends Model
{
    protected $table = 'portfolio_category';
    protected $fillable = [
        'user_id',
        'category_value',
        'category_data',
    ];

    function getcategoryValueAttribute($value)
    {
        return ucfirst($value);
    }
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'category');
    }
}
