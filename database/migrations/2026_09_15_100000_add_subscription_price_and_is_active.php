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
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('subscription_price', 10, 2)->default(0.00)->after('lessons_count');
        });

        Schema::table('subject_teacher', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('subscription_price');
        });

        Schema::table('subject_teacher', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
