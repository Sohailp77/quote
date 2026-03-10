<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;

trait HasTenant
{
    /**
     * Boot the trait and apply the global scope.
     */
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->check() && !$model->tenant_id && auth()->user()->tenant_id !== null) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * A model with this trait belongs to a tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
