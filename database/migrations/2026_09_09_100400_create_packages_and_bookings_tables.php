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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('title');
            $table->string('badge')->nullable(); // الأكثر طلباً, حضور مباشر, مرونة كاملة
            $table->string('type')->default('online'); // online, offline
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('ج.م');
            $table->integer('total_lectures')->default(8);
            $table->integer('weekly_lectures')->default(2);
            $table->decimal('hours_per_lecture', 3, 1)->default(2.0);
            $table->boolean('is_popular')->default(false);
            $table->string('color_theme')->default('teal'); // teal, amber, blue, purple
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('feature_text');
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_phone');
            $table->string('parent_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('packages');
    }
};
