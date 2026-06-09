<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => Role::FRESHER, 'label' => 'พนักงานใหม่ / Fresher', 'rank' => Role::RANK_FRESHER],
            ['name' => Role::SENIOR, 'label' => 'พนักงานอาวุโส / Senior', 'rank' => Role::RANK_SENIOR],
            ['name' => Role::ADMIN, 'label' => 'ผู้ดูแลระบบ / Admin', 'rank' => Role::RANK_ADMIN],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
