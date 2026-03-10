<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\SchoolScope;

class Attendance extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'teaching_assignment_id',
        'student_id',
        'attendance_date',
        'status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}