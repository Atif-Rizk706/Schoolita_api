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
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('packages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-creating dropped tables if rolling back is handled via fresh migrations if needed.
    }
};
