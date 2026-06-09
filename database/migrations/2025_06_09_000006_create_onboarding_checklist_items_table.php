<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The checklist TEMPLATE (shared definition). Per-user progress lives in
// user_checklist_progress so one template row serves every user.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('position')->default(0)->index(); // display order
            // NULL = applies to everyone; set = department-specific item (e.g. ISD, Warehouse).
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_checklist_items');
    }
};
