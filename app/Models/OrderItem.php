<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function option()
    {
        return $this->belongsTo(FoodOption::class , 'food_option_id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }
}
