<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'mobile', 'sms_sent_code', 'sms_sent_date', 'sms_sent_tries'];
    protected $casts = [
        'sms_sent_date' => 'datetime',
    ];
}
