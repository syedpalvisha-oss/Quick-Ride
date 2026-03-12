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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('driver_cancelled_at')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('pickup_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('vehicle_type')->default(0);
            $table->decimal('fee', 19, 8)->default(0);
            $table->decimal('fare', 19, 8)->default(0);
            $table->decimal('tip', 19, 8)->default(0);
            $table->string('currency_id')->nullable();
            $table->geometry('pickup_location', 'POINT', 4326)->nullable();
            $table->geometry('dropoff_location', 'POINT', 4326)->nullable();
            $table->integer('rate')->nullable();
            $table->text('review')->nullable();
            $table->integer('driver_rate')->nullable();
            $table->text('driver_review')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
