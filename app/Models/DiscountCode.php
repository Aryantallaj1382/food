<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    protected $table = 'discount_codes';

    protected $fillable = [
        'name',
        'percentage',
        'max_discount',
        'valid_until',
        'one_time_use',
        'collection_id',
    ];

    protected $casts = [
        'one_time_use' => 'boolean',
        'valid_until' => 'date',
    ];

    /**
     * رابطه با مجموعه (Collection)
     * اگر null باشد، برای همه مجموعه‌هاست
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * بررسی اعتبار کد تخفیف
     */
    public function isValid()
    {
        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * محاسبه مبلغ تخفیف با توجه به سقف
     *
     * @param float $totalAmount
     * @return float
     */
    public function calculateDiscount(float $totalAmount): float
    {
        $discount = ($totalAmount * $this->percentage) / 100;
        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }
        return $discount;
    }
}
