<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class TaxRate extends Model
{
    use HasTenant;
    protected $fillable = ['name', 'rate', 'type', 'is_active'];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
