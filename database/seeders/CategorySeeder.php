<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Getting Started',
            'Company & Culture',
            'IT & Accounts',
            'Systems & Tools',
            'HR & Workplace',
            'Policies & Compliance',
            'Glossary & Acronyms',
            'Engineering Standards',
            'Project Docs',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
