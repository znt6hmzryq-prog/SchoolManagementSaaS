<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\SchoolScope;

class Assessment extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'teaching_assignment_id',
        'title',
        'type',
        'max_score',
        'weight',
        'assessment_date',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'max_score' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}