<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'restaurant_accept'=>'boolean',
        'no_message'=>'boolean',
    ];
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
    public function getPriceWithDiscountAttribute()
    {
        return $this->total_amount -($this->total_amount * $this->discount_percentage );
    }
    public function getPayStatusFaAttribute()
    {
        $map = [
            'cash' => 'پرداخت در محل',
            'paid' => 'پرداخت شده',
            'failed' => 'لغو شده',
            'pending' => 'در انتظار پرداخت',
        ];

        return Arr::get($map, $this->payment_status, 'نامشخص');
    }

    public function getStatusFaAttribute()
    {
        $map = [
            'pending' => 'در انتظار تایید',
            'processing' => 'در حال آماده سازی',
            'completed' => 'در انتظار پیک',
            'cancelled' => 'کنسل شده',
            'delivery' => 'تحویل به پیک',
            'rejected' => 'رد شده',
        ];
        if ($this->payment_status == 'pending'){
            return 'در مرحله ی پرداخت';
        }
        return Arr::get($map, $this->status, 'نامشخص');
    }
    public function getStatusUserFaAttribute()
    {
        $map = [
            'pending' => 'در انتظار تایید',
            'processing' => 'تایید شده',
            'completed' => 'تایید شده',
            'cancelled' => 'کنسل شده',
            'delivery' => 'تایید شده',
            'rejected' => 'رد شده',
        ];
        if ($this->payment_status == 'pending'){
            return 'در مرحله ی پرداخت';
        }
        return Arr::get($map, $this->status, 'نامشخص');
    }
    public function getSendingMethodFaAttribute()
    {
        $map = [
            'pike' => 'پیک موتوری',
            'in_person' => 'تحویل حضوری',
        ];
        if (!$this->sending_method) {
            return 'نامشخص';
        }
        return Arr::get($map, $this->sending_method, 'نامشخص');
    }
    public function getPaymentMethodFaAttribute()
    {
        $map = [
            'online' => 'پرداخت آنلاین',
            'cash' => 'پرداخت نقدی',
        ];
        if (!$this->payment_method) {
            return 'نامشخص';
        }
        return Arr::get($map, $this->payment_method, 'نامشخص');
    }

    public function getGatewayFaAttribute()
    {
        $map = [
            'zarinpal' => 'زرین پال',
            'melat' => 'ملت',
        ];
        if (!$this->gateway) {
            return 'نامشخص';
        }
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
