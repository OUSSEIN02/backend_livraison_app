<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livreur_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 8, 2)->nullable(); // km/h
            $table->decimal('heading', 5, 2)->nullable(); // direction en degrés
            $table->decimal('accuracy', 8, 2)->nullable(); // précision en mètres
            $table->enum('status', ['available', 'busy', 'offline'])->default('available');
            $table->timestamp('last_seen_at');
            $table->timestamps();
            
            $table->index(['user_id', 'last_seen_at']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livreur_locations');
    }
};