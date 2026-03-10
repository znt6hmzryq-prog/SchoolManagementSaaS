<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_students', 'description' => 'Can create, update, delete students'],
            ['name' => 'manage_teachers', 'description' => 'Can create, update, delete teachers'],
            ['name' => 'manage_grades', 'description' => 'Can add, update, delete grades'],
            ['name' => 'manage_attendance', 'description' => 'Can mark attendance'],
            ['name' => 'manage_finance', 'description' => 'Can manage invoices and payments'],
            ['name' => 'view_reports', 'description' => 'Can view analytics and reports'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }
    }
}
