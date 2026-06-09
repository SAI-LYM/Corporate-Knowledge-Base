<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (CLAUDE.md §3 — local/dev demo data).
     *
     * Order matters: reference data (roles, departments) → users → content
     * (categories, tags, articles, projects, onboarding checklist + progress).
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            ArticleSeeder::class,
            ProjectSeeder::class,
            OnboardingChecklistSeeder::class,
        ]);
    }
}
