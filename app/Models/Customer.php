<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class Customer extends Model
{
    use HasTenant;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'notes',
    ];
}
