<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body_markdown');                 // raw Markdown (rendered safely on read)

            // --- Content visibility axes (CLAUDE.md §1) ---
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('audience_level', ['onboarding', 'general', 'advanced'])
                ->default('general')->index();
            $table->unsignedTinyInteger('min_role')->default(1)->index(); // 1=Fresher,2=Senior,3=Admin

            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('view_count')->default(0);            // for #report most-viewed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
