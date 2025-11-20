<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'tax_enabled' => 'boolean',
        'panel_editable' => 'boolean',
        'free_shipping' => 'boolean',
        'discount' => 'boolean',
        'is_open' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getIsOpenAttribute(): bool
    {
        $status = SystemSetting::where('kay', 'system_status')->value('value');
        if ($status == 0) {
            return false;
        }

        $now = Carbon::now('Asia/Tehran')->format('H:i');

        $morningStart = $this->morning_start;
        $morningEnd   = $this->morning_end;
        $eveningStart = $this->afternoon_start;
        $eveningEnd   = $this->afternoon_end;

        $inMorning = $morningStart && $morningEnd &&
            ($now >= $morningStart && $now <= $morningEnd);

        $inEvening = $eveningStart && $eveningEnd &&
            ($now >= $eveningStart && $now <= $eveningEnd);

        if (!$inMorning && !$inEvening) {
            return false;
        }
        return (bool) $this->attributes['is_open'];
    }


    // اگر جدول مرتبط دیگری داری (مثل service_times) اینجا رابطه‌ها رو می‌سازی
    public function serviceTimes()
    {
        return $this->hasMany(RestaurantServiceTime::class);
    }
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'restaurant_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }
    public function foods()
    {
        return $this->hasMany(Food::class);
    }
    public function comments()
    {
        return $this->hasManyThrough(
            Comment::class,
            Order::class,
            'restaurant_id', // foreign key در جدول orders
            'order_id',      // foreign key در جدول comments
            'id',            // local key در جدول restaurants
            'id'             // local key در جدول orders
        );
    }

    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
    protected $appends = ['category_names', 'rate' , 'rate_count' ,'pay_type'];
    protected function categoryNames(): Attribute
    {
        return Attribute::get(function () {
            return $this->categories->pluck('name')->toArray();
        });
    }
    protected function getRateAttribute()
    {
        return Comment::whereHas('order', function ($query) {
            $query->where('restaurant_id', $this->id);
        })->avg('rating');
    }
    protected function getRateCountAttribute()
    {
        return Comment::whereHas('order', function ($query) {
            $query->where('restaurant_id', $this->id);
        })->count();
    }
    protected function getPayTypeAttribute()
    {
        return match ($this->attributes['pay_type']) {
            'cash'   => ['پرداخت در محل'],
            'online' => ['پرداخت آنلاین'],
            'both'   => ['پرداخت در محل', 'پرداخت آنلاین'],
            default  => [],
        };
    }

}
