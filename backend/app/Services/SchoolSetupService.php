<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;

class SchoolSetupService
{
    public function setup(array $data)
    {
        $school = School::create([
            'name' => $data['school_name'],
            'slug' => strtolower(str_replace(' ', '-', $data['school_name'])),
            'plan_type' => 'basic',
            'subscription_status' => 'trial',
            'subscription_expires_at' => now()->addDays(30),
            'max_students' => 50,
            'max_teachers' => 10,
        ]);

        $admin = User::create([
            'name' => $data['admin_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        $academicYear = AcademicYear::create([
            'school_id' => $school->id,
            'name' => now()->year . '-' . now()->addYear()->year,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        $classes = ['Grade 1', 'Grade 2', 'Grade 3'];

        foreach ($classes as $class) {

            $classRoom = ClassRoom::create([
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'name' => $class,
            ]);

            Section::create([
                'school_id' => $school->id,
                'class_room_id' => $classRoom->id,
                'name' => 'A',
            ]);
        }

        return $school;
    }
}