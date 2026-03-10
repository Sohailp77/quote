<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class ActivityLog extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'type',
        'description',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * After creating a new log entry, ensure only the most recent 20 are retained.
     */
    protected static function booted(): void
    {
        static::created(function () {
            // Keep only latest 20 records - delete older ones
            $latestIds = static::latest()->limit(20)->pluck('id');
            if ($latestIds->count() === 20) {
                static::whereNotIn('id', $latestIds)->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
