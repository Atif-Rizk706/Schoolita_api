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
        Schema::create('group_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('day_of_week'); // e.g. 'sunday', 'monday', etc. or day name
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable(); // e.g. 'Room A', 'Lab 1', 'Online Zoom'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_days');
    }
};
