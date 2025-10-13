<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
    ];
    public function getIconAttribute($value)
    {
        return url('public/'.$value);

    }

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'category_restaurant');
    }
}
