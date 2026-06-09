<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('repo_url');
            $table->string('tech_stack');                  // e.g. "PHP, REST, SQL"
            $table->string('status')->default('Active');
            $table->text('readme_markdown');               // raw Markdown (rendered safely on read)

            // --- Same visibility axes as articles (Projects default to advanced/Senior) ---
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->enum('audience_level', ['onboarding', 'general', 'advanced'])
                ->default('advanced')->index();
            $table->unsignedTinyInteger('min_role')->default(2)->index(); // default Senior

            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
