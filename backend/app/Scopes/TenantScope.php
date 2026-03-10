<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            $builder->where(
                $model->getTable() . '.school_id',
                auth()->user()->school_id
            );
        }
    }
}
