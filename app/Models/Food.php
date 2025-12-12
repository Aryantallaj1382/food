<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';


    protected $fillable = [
        'restaurant_id',
        'name',
        'image',
        'food_categories_id',
        'description',
        'is_available',
        'about_category'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['restaurant_name'];

    public function getRestaurantNameAttribute()
    {
        $restaurant = $this->restaurant()->first(); // رابطه را دستی بگیر
        return $restaurant ? $restaurant->name : null;
    }
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;

    }
    public function getAvailabilityScoreAttribute()
    {
        if ($this->is_available == 0) return 0;

        if (!$this->relationLoaded('options')) {
            $this->load('options:id,food_id,is_available');
        }

        // حداقل یک آپشن در دسترس
        $hasAvailableOption = $this->options->contains(fn($op) => $op->is_available == 1);

        return $hasAvailableOption ? 1 : 0;
    }


    public function category()
    {
        return $this->belongsTo(FoodCategory::class , 'food_categories_id');
    }
    public function restaurant1()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function options()
    {
        return $this->hasMany(FoodOption::class);
    }
    public function getDiscountPercentageAttribute()
    {
        if ($this->options->isEmpty()) {
            return 0;
        }
        $bestOption = $this->options->sortByDesc(function ($option) {
            if ($option->price && $option->price_discount) {
                return (($option->price - $option->price_discount) / $option->price) * 100;
            }
            return 0;
        })->first();

        $price = $bestOption->price;
        $price_discount = $bestOption->price_discount;

        if ($price > 0 && $price_discount < $price && $price_discount != null) {
            $discount = (($price - $price_discount) / $price) * 100;
            return round($discount, 2);
        }

        return 0;
    }

    public function scopeInactive($query)
    {
        return $query->select('foods.*')
            ->from('foods')
            ->join('food_options', 'foods.id', '=', 'food_options.food_id')
            ->groupBy('foods.id')
            ->havingRaw('MIN(food_options.is_available) = 0');
    }
    public function inactiveOptions()
    {
        return $this->hasMany(FoodOption::class, 'food_id')
            ->where('is_available', 0);
    }



}
