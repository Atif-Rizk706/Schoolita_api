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
        // Add bio (translatable) to subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('description');
        });

        // Subject Learning Outcomes (مخرجات التعلم)
        Schema::create('subject_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title'); // stored as JSON: {"ar":"...", "en":"..."}
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // Subject Features / Advantages (مميزات المادة)
        Schema::create('subject_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title'); // stored as JSON: {"ar":"...", "en":"..."}
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_features');
        Schema::dropIfExists('subject_outcomes');
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
};
