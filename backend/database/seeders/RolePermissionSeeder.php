<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissions = [
            'school_admin' => ['manage_students', 'manage_teachers', 'manage_grades', 'manage_attendance', 'manage_finance', 'view_reports'],
            'teacher' => ['manage_grades', 'manage_attendance', 'view_reports'],
            'student' => ['view_reports'],
            'parent' => ['view_reports'],
        ];

        foreach ($rolePermissions as $role => $permissions) {
            foreach ($permissions as $permissionName) {
                $permission = \App\Models\Permission::where('name', $permissionName)->first();
                if ($permission) {
                    \App\Models\RolePermission::create([
                        'role' => $role,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }
    }
}
