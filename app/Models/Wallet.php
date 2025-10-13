<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * هر کیف پول متعلق به یک کاربر است (یک‌به‌یک).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
