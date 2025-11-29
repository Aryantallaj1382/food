<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   FoodCategory extends Model
{
    use HasFactory;

    protected $table = 'food_categories';
    protected $fillable = [
        'name',
        'icon'
    ];
    public function food()
    {
        return $this->hasMany(Food::class , 'food_categories_id');
    }
    public function getIconAttribute($value)
    {
        return $value ? asset('public/'. $value) : null;

    }

}
