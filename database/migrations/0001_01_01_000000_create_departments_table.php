<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Created before users (alphabetical, same timestamp) so users can FK to it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. Information Systems
            $table->string('code')->unique();          // e.g. ISD, HR, WH
            // Company-wide content lives in a department flagged global → visible to everyone.
            $table->boolean('is_global')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
