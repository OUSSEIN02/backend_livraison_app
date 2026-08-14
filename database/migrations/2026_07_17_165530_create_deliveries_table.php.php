<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_person_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'assigned', 'picked_up', 'in_transit', 
                'delivered', 'failed'
            ])->default('assigned');
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->json('gps_coordinates')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};