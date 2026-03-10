<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Subject;
use App\Models\Scopes\SchoolScope;

class Teacher extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)
                    ->withTimestamps();
    }
}