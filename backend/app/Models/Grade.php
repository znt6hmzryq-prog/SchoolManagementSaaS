<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\SchoolScope;

class Grade extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}