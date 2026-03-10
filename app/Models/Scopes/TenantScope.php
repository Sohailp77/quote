<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            // For CLI/Tinker, we might still want to apply it if a user is manually logged in
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
            return;
        }

        if (auth()->hasUser() && !auth()->user()->isSuperAdmin()) {
            $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
        }
    }
}
