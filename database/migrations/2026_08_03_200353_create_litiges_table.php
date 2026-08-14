<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('litiges', function (Blueprint $table) {
            $table->id();
            
            // Clés étrangères
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // On lie au vendeur (table 'sellers' ou 'users' selon votre architecture). 
            // Ici, je pars du principe que vous avez une table 'sellers' comme vu précédemment.
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            
            // Nullable car un litige peut être ouvert avant qu'un livreur ne soit assigné, 
            // ou concerner uniquement le vendeur/la plateforme.
            $table->foreignId('livreur_id')->nullable()->constrained('livreurs')->nullOnDelete();
            
            // Détails du litige
            $table->string('type', 100)->comment('Ex: Colis non reçu, Produit endommagé, Retard, etc.');
            $table->text('description');
            
            // Gestion de l'état et de l'urgence
            $table->string('status', 20)->default('en_attente')->comment('en_attente, en_cours, resolu');
            $table->string('priorite', 20)->default('moyenne')->comment('haute, moyenne, basse');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes de filtrage
            $table->index('status');
            $table->index('priorite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('litiges');
    }
};