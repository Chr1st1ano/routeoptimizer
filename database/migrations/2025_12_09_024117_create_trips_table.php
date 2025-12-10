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
    Schema::create('trips', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('start_point')->nullable();
        $table->string('destination');
        $table->decimal('distance_km', 8, 2);
        $table->integer('duration_minutes');
        
        // --- NEW COLUMNS ---
        $table->string('vehicle_type')->default('car'); 
        $table->string('traffic_condition')->nullable(); // Stores 'Heavy', 'Light', etc.
        
        $table->string('route_type')->nullable();
        $table->text('notes')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};