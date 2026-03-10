<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\SchoolScope;

class Student extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'section_id',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
