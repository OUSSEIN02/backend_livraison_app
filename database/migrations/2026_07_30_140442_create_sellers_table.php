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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();

            // Informations de l'entreprise
            $table->string('company_name');
            $table->string('siret')->nullable();

            // Coordonnées
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->text('address');

            // Authentification
            $table->string('password');

            // Pièces justificatives
            $table->string('id_front_path');
            $table->string('id_back_path');
            $table->string('selfie_path');

            // Statut du compte
            $table->enum('status', [
                'en_attente',
                'valide',
                'rejete',
                'suspendu'
            ])->default('en_attente');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};