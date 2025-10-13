<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodOption extends Model
{
    public $guarded = [];
    protected $casts = [
        'price' => 'int',
        'price_discount' => 'int',
    ];
    public function food()
    {
        return $this->belongsTo(Food::class);
    }
    public function getDiscountAttribute()
    {
        if ($this->price_discount == null) {
            return $this->price;
        }
        return $this->price_discount;

    }

}
