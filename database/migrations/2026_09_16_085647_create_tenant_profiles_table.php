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
        Schema::create('tenant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            
            // Branding
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('primary_color')->default('#f59e0b');
            $table->string('secondary_color')->default('#0d9488');
            
            // Contact (Secondary)
            $table->string('whatsapp')->nullable();
            
            // Translatable Fields (Stored as JSON)
            $table->json('address')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('about_us')->nullable();
            $table->json('vision')->nullable();
            $table->json('mission')->nullable();
            $table->json('hero_title')->nullable();
            $table->json('hero_subtitle')->nullable();
            
            // Social Links (JSON)
            $table->json('social_links')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_profiles');
    }
};
