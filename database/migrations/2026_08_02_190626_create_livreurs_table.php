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
        Schema::create('livreurs', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers la table users
            // onDelete('cascade') signifie que si l'utilisateur est supprimé, son profil livreur l'est aussi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Informations sur le véhicule
            $table->string('numero_plaque', 50)->comment('Numéro d\'immatriculation du véhicule');
            $table->string('etat_moto')->comment('État du véhicule: excellent, bon, moyen, correct');
            $table->string('experience')->comment('Expérience: oui ou non');
            
            // Zones de livraison (stockées au format JSON pour faciliter la manipulation en PHP/JS)
            $table->json('zones_livraison')->comment('Tableau des zones de livraison sélectionnées');
            
            // Chemins des images compressées
            $table->string('photo_identite_path')->comment('Chemin de la photo selfie / identité');
            $table->string('photo_piece_identite_path')->comment('Chemin de la photo de la pièce d\'identité');
            $table->string('photo_moto_path')->comment('Chemin de la photo du véhicule');
            
            // Statut du dossier (en_attente, valide, rejete, suspendu)
            $table->string('status')->default('en_attente')->comment('Statut de validation du dossier');
            
            $table->timestamps();
            
            // Index pour accélérer les recherches par statut
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livreurs');
    }
};