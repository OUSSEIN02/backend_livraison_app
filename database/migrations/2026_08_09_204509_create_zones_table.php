<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Ex: Estuaire (Libreville, Owendo, Akanda)
            $table->string('code', 10)->unique(); // Ex: EST, HAU
            $table->decimal('tarif_km', 10, 2)->default(0); // Tarif en FCFA par km
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};