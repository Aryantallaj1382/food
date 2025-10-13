<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_main',
        'address',
        'latitude',
        'longitude',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * هر آدرس متعلق به یک کاربر است.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
