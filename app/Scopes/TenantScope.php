<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (session()->has('tenant_id') && !auth()->user()?->isSuperAdmin()) {
            $builder->where('tenant_id', session('tenant_id'));
        }
    }
}