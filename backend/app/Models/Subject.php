<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\Scopes\SchoolScope;

class Subject extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'code',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class)
                    ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)
                    ->withTimestamps();
    }
}