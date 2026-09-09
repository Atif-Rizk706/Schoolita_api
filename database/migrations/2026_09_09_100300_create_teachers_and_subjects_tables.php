<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(5);
            $table->integer('students_count')->default(500);
            $table->decimal('rating', 3, 1)->default(4.9);
            $table->string('whatsapp_number')->nullable();
            $table->string('color_theme')->default('amber'); // amber, teal, blue, purple
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->default('book-open');
            $table->string('color_theme')->default('amber'); // amber, teal, blue, purple
            $table->integer('lessons_count')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('teachers');
    }
};
