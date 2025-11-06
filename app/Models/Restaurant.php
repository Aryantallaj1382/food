<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'address',
        'grt_ready_minute',
        'sending_way',
        'minimum_price',
        'work_time',
        'latitude',
        'longitude',
        'is_open',
        'user_id',
        'send_price',
        'discount_percentage',
        'delivery_radius_km',
        'discount',
        'morning_start',
        'morning_end',
        'afternoon_start',
        'afternoon_end',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // اگر جدول مرتبط دیگری داری (مثل service_times) اینجا رابطه‌ها رو می‌سازی
    public function serviceTimes()
    {
        return $this->hasMany(RestaurantServiceTime::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }
    public function foods()
    {
        return $this->hasMany(Food::class);
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
        return 3;

    }
    protected function getRateCountAttribute()
    {
        return 50;

    }
    protected function getPayTypeAttribute()
    {
        return ['پرداخت در محل','پرداخت آنلاین'];

    }
}
