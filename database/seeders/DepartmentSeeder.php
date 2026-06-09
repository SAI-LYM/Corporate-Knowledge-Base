<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            // Company-wide content is global → visible to everyone (CLAUDE.md §1).
            ['code' => 'ALL', 'name' => 'Company-wide', 'is_global' => true],
            ['code' => 'ISD', 'name' => 'Information Systems', 'is_global' => false],
            ['code' => 'HR', 'name' => 'Human Resources', 'is_global' => false],
            ['code' => 'WH', 'name' => 'Warehouse', 'is_global' => false],
            ['code' => 'TRA', 'name' => 'Transportation', 'is_global' => false],
            ['code' => 'CD', 'name' => 'Container Depot', 'is_global' => false],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['code' => $department['code']], $department);
        }
    }
}
