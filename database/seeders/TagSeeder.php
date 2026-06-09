<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'onboarding', 'microsoft-365', 'vpn', 'wms', 'infor', 'pdpa',
            'safety', 'git', 'deployment', 'leave', 'payroll', 'glossary',
            'access-request',
        ];

        foreach ($tags as $slug) {
            Tag::updateOrCreate(
                ['slug' => $slug],
                ['name' => $slug],
            );
        }
    }
}
