<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'login_date', 'login_time', 'logout_time', 'login_duration', 'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
