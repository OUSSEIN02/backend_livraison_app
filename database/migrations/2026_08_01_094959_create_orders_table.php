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

            // Relations
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendeur_id')->constrained('vendeurs')->onDelete('cascade');
            $table->foreignId('livreur_id')->nullable()->constrained('livreurs')->nullOnDelete();

            // Informations de récupération (Pickup)
            $table->string('pickup_name');
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 8); 
            $table->decimal('pickup_lng', 11, 8);

            // Informations de livraison (Dropoff)
            $table->string('dropoff_name');
            $table->string('dropoff_address');
            $table->decimal('dropoff_lat', 10, 8);
            $table->decimal('dropoff_lng', 11, 8);

            // Détails du colis
            $table->string('weight')->nullable();
            $table->boolean('is_fragile')->default(false);
            $table->decimal('declared_value', 10, 2)->default(0.00);
            $table->string('package_photo')->nullable();

            // Type et planification de la livraison
            $table->enum('delivery_type', ['immediat', 'programme'])->default('immediat');
            $table->timestamp('scheduled_date')->nullable();
            $table->text('instructions')->nullable();

            // Suivi et Statut (Clé pour le "Matching automatique" et le "Suivi en temps réel")
            $table->enum('status', [
                'en_attente',    // En attente d'attribution (auto ou manuelle)
                'assignee',      // Attribuée à un livreur, en attente d'acceptation
                'en_cours',      // Livreur a accepté et est en route
                'livree',        // Livraison terminée avec succès
                'annulee',       // Commande annulée
                'litige'         // Problème signalé (voir section 11 du PDF)
            ])->default('en_attente');

            // Montant total (sera calculé plus tard par le système de tarification basé sur les zones)
            $table->decimal('total_amount', 10, 2)->default(0.00);

            // Métadonnées
            $table->timestamp('assigned_at')->nullable(); // Quand le livreur a été assigné
            $table->timestamp('picked_up_at')->nullable(); // Quand le livreur a récupéré le colis
            $table->timestamp('delivered_at')->nullable(); // Quand la livraison est terminée
            
            $table->timestamps();
            $table->softDeletes(); // Permet la suppression logique (historique des transactions)

            // Index pour optimiser les requêtes (surtout pour le matching automatique)
            $table->index('status');
            $table->index('livreur_id');
            $table->index('vendeur_id');
            $table->index('delivery_type');
            
            // Index composé pour la recherche de livreurs proches (optimisation future)
            $table->index(['pickup_lat', 'pickup_lng']);
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