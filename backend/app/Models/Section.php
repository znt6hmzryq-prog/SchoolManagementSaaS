<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SchoolScope;

class Section extends Model
{
    protected $fillable = [
        'school_id',
        'class_room_id',
        'name'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}