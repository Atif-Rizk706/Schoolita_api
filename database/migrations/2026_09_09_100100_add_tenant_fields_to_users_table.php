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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('parent_email')->nullable()->after('whatsapp');
            $table->string('parent_phone')->nullable()->after('parent_email');
            $table->string('parent_whatsapp')->nullable()->after('parent_phone');
            $table->string('country')->nullable()->default('EG')->after('parent_whatsapp');
            $table->string('image')->nullable()->after('password');
            $table->unsignedBigInteger('grade_id')->nullable()->after('role'); // For students: their current grade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn([
                'tenant_id',
                'phone',
                'whatsapp',
                'parent_email',
                'parent_phone',
                'parent_whatsapp',
                'country',
                'image',
                'grade_id'
            ]);
        });
    }
};
