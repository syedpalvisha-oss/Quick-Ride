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
        Schema::table('rides', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->nullable();
            $table->decimal('pickup_lng', 11, 8)->nullable();
            $table->decimal('drop_lat', 10, 8)->nullable();
            $table->decimal('drop_lng', 11, 8)->nullable();
            $table->decimal('distance', 8, 2)->nullable(); // distance in km
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn(['pickup_lat', 'pickup_lng', 'drop_lat', 'drop_lng', 'distance']);
        });
    }
};
