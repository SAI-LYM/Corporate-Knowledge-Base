<?php

/*
 |----------------------------------------------------------------------------
 | CANONICAL MIGRATION PATTERN  (reference only — NOT run by the migrator)
 |----------------------------------------------------------------------------
 | Copy this shape for every new table (CLAUDE.md §3 "Database Rules").
 |
 |  - Real foreign keys with an explicit ON DELETE behaviour.
 |  - An index on every column you filter / join on (FKs get one for free
 |    via constrained(), others added explicitly).
 |  - "enum-style" columns: a small whitelist stored as a string, guarded by
 |    Laravel's enum() (a CHECK constraint on SQLite) — the app also validates.
 |  - Pivots: composite UNIQUE so the same pair can't be linked twice.
 |  - Never edit a shipped migration's data; change schema via a NEW migration.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();          // lookups by slug → unique index
            $table->text('body_markdown');             // raw Markdown, rendered safely later

            // enum-style column: a closed whitelist (validated in the app too)
            $table->enum('audience_level', ['onboarding', 'general', 'advanced'])
                ->default('general')
                ->index();

            // integer "rank" used for `role >= min_role` comparisons (CLAUDE.md §1)
            $table->unsignedTinyInteger('min_role')->default(1);

            // foreign keys: constrained() creates the FK + an index automatically
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();
        });

        // Many-to-many pivot: composite unique prevents duplicate links.
        Schema::create('document_tag', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['document_id', 'tag_id']); // also serves as the unique guard
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_tag');
        Schema::dropIfExists('documents');
    }
};
