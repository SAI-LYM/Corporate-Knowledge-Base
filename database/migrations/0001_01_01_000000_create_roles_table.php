<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Created before users (alphabetical, same timestamp) so users can FK to it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();          // Fresher | Senior | Admin
            $table->string('label')->nullable();       // display label (EN/TH)
            // Integer seniority rank for "role >= min_role" checks: 1=Fresher, 2=Senior, 3=Admin
            $table->unsignedTinyInteger('rank')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
