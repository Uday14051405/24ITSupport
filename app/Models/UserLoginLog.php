<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'log_datetime', 'login_type', 'user_status'
    ];

    protected $casts = [
        'log_datetime' => 'datetime',
    ];
}
