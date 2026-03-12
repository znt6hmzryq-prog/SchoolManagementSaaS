<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;

class AcademicYear extends BaseTenantModel
{
    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }
}