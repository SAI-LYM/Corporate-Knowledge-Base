<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The 8 demo users used by the mock login (BUILD_GUIDE.md Part A). Bilingual
 * names. No passwords — mock/OAuth accounts have none (CLAUDE.md §3).
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');
        $depts = Department::pluck('id', 'code');

        $users = [
            ['name' => 'Somchai Jaidee', 'name_th' => 'สมชาย ใจดี', 'email' => 'somchai.jaidee@company.com', 'role' => Role::ADMIN, 'dept' => 'ISD'],
            ['name' => 'Anan Srisuk', 'name_th' => 'อนันต์ ศรีสุข', 'email' => 'anan.srisuk@company.com', 'role' => Role::SENIOR, 'dept' => 'ISD'],
            ['name' => 'Pranee Wongsuk', 'name_th' => 'ปราณี วงศ์สุข', 'email' => 'pranee.wongsuk@company.com', 'role' => Role::SENIOR, 'dept' => 'HR'],
            ['name' => 'Kanya Phongphan', 'name_th' => 'กัญญา พงษ์พันธ์', 'email' => 'kanya.phongphan@company.com', 'role' => Role::SENIOR, 'dept' => 'WH'],
            ['name' => 'Nattapong Rakthai', 'name_th' => 'ณัฐพงษ์ รักษ์ไทย', 'email' => 'nattapong.rakthai@company.com', 'role' => Role::FRESHER, 'dept' => 'ISD'],
            ['name' => 'Malee Suwan', 'name_th' => 'มาลี สุวรรณ', 'email' => 'malee.suwan@company.com', 'role' => Role::FRESHER, 'dept' => 'HR'],
            ['name' => 'Wichai Charoenporn', 'name_th' => 'วิชัย เจริญพร', 'email' => 'wichai.charoenporn@company.com', 'role' => Role::FRESHER, 'dept' => 'WH'],
            ['name' => 'Suda Meechai', 'name_th' => 'สุดา มีชัย', 'email' => 'suda.meechai@company.com', 'role' => Role::FRESHER, 'dept' => 'TRA'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'name_th' => $u['name_th'],
                    'role_id' => $roles[$u['role']],
                    'department_id' => $depts[$u['dept']],
                    'password' => null, // no plain-text password (CLAUDE.md §3)
                ],
            );
        }
    }
}
