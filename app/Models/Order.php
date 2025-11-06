<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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

    public function getStatusFaAttribute()
    {
        $map = [
            'pending' => 'در انتظار بررسی',
            'processing' => 'در حال پردازش',
            'completed' => 'تکمیل‌شده',
            'cancelled' => 'لغو‌شده',
        ];

        return Arr::get($map, $this->status, 'نامشخص');
    }
    public function getSendingMethodFaAttribute()
    {
        $map = [
            'pike' => 'پیک موتوری',
            'in_person' => 'تحویل حضوری',
        ];
        return Arr::get($map, $this->sending_method, 'نامشخص');
    }
    public function getPaymentMethodFaAttribute()
    {
        $map = [
            'online' => 'پرداخت آنلاین',
            'cash' => 'پرداخت نقدی',
        ];
        return Arr::get($map, $this->payment_method, 'نامشخص');
    }
    public function getGatewayFaAttribute()
    {
        $map = [
            'zarinpal' => 'زرین پال',
            'melat' => 'ملت',
        ];
        return Arr::get($map, $this->gateway, 'نامشخص');
    }
    public function getPaymentStatusFaAttribute()
    {
        $map = [
            'paid' => 'پرداخت‌شده',
            'pending' => 'در انتظار',
            'field' => 'پرداخت نشده',
        ];

        return Arr::get($map, $this->payment_status, 'نامشخص');
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
