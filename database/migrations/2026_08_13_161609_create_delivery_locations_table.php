<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('livreur_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('heading', 6, 2)->default(0);     // Direction (degrés)
            $table->decimal('speed', 6, 2)->default(0);       // m/s
            $table->decimal('accuracy', 6, 2)->default(0);    // mètres
            $table->string('phase', 20);                      // toPickup, toDropoff, etc.
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Index pour les requêtes fréquentes
            $table->index(['order_id', 'recorded_at']);
            $table->index('livreur_id');
        });

        // Ajouter les colonnes pour la dernière position sur orders (accès rapide)
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('last_lat', 10, 8)->nullable()->after('distance_km');
            $table->decimal('last_lng', 11, 8)->nullable()->after('last_lat');
            $table->decimal('last_heading', 6, 2)->nullable()->after('last_lng');
            $table->decimal('last_speed', 6, 2)->nullable()->after('last_heading');
            $table->timestamp('last_location_at')->nullable()->after('last_speed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_locations');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'last_lat', 'last_lng', 'last_heading', 'last_speed', 'last_location_at'
            ]);
        });
    }
};