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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('domain')->nullable()->unique()->index();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('primary_color')->default('#f59e0b');
            $table->string('secondary_color')->default('#0d9488');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('about_us')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->string('working_hours')->nullable();
            $table->json('social_links')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->json('stats')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('subscription_plan')->default('standard');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
