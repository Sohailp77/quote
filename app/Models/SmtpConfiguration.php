<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpConfiguration extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
        'fail_count',
        'last_used_at',
        'last_fail_at',
        'last_error',
        'priority',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'is_active' => 'boolean',
        'fail_count' => 'integer',
        'last_used_at' => 'datetime',
        'last_fail_at' => 'datetime',
    ];
}
