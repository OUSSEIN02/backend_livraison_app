<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('vehicle_type', ['moto', 'velo', 'voiture'])->nullable();
            $table->string('license_number')->nullable();
            $table->string('id_card_number')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_ratings')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_online')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_persons');
    }
};