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
    protected $appends = ['discount_percentage','discount'];

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
    public function getPriceOrderAttribute()
    {
        if ($this->price_discount == null) {
            return $this->price;
        }
        return $this->price_discount;

    }
    public function getDiscountPercentageAttribute()
    {
        if ($this->price && $this->price_discount) {
            return 100 - round(($this->price_discount / $this->price) * 100, 2);
        }
        return 0;
    }

}
