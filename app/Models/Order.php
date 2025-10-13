<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 🧍 ارتباط با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🍔 ارتباط با آیتم‌های سفارش
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 💳 بررسی پرداخت
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    // 🚚 بررسی تکمیل سفارش
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    // 💰 جمع مبلغ کل
    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 0);
    }
}
