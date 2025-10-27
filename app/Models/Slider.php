<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['image','link','order' ];
    public function getImageAttribute($value)
    {
        return $value ? asset('public/'.$value) : null;

    }
}
