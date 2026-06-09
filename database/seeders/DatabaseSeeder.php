<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (CLAUDE.md §3 — local/dev demo data).
     *
     * Reference data + mock users first; content (categories, tags, articles,
     * projects, onboarding checklist) is added by the content seeders in Prompt 5.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);
    }
}
