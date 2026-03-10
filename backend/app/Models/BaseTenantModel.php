<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;

abstract class BaseTenantModel extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->role !== 'super_admin') {
                $model->school_id = auth()->user()->school_id;
            }
        });
    }
}
