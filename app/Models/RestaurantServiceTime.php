<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantServiceTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day_of_week',
        'meal_type',
        'start_time',
        'end_time',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
