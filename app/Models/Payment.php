<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'payment_method',
        'gateway',
        'status',
        'notes',
        'type',
        'transaction_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getTypeFaAttribute()
    {
        return match ($this->type) {
            'withdraw' => 'برداشت',
            'deposit'     => 'وایز',
            default    => '---',
        };
    }
    public function getGatewayFaAttribute()
    {
        return match ($this->gateway) {
            'pars' => 'پارسیان',
            default    => '---',
        };
    }

}
