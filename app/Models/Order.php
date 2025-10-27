<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function adress()
    {
        return $this->belongsTo(Address::class , 'address_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    // 🚚 بررسی تکمیل سفارش
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    public function comment()
    {
        return $this->hasOne(Comment::class);
    }


}
