<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantDiscount extends Model
{
    protected $guarded = [];
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
}
