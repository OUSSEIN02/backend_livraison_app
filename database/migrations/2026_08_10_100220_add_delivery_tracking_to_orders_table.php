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
        Schema::table('orders', function (Blueprint $table) {

            // 🎯 Zone de livraison
            $table->foreignId('zone_id')
                ->nullable()
                ->constrained('zones')
                ->nullOnDelete();

            // 👤 Livreur assigné
            // NULL tant qu'aucun livreur n'est assigné
            $table->foreignId('livreur_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 📏 Informations de distance et tarif
            $table->decimal('distance_km', 8, 2)
                ->default(0);

            $table->decimal('tarif_base', 10, 2)
                ->default(0);

            $table->decimal('tarif_km_applied', 8, 2)
                ->default(0);

            // 🔄 État du workflow d'assignation
            $table->enum('assignation_status', [
                'en_attente_recherche',
                'recherche_en_cours',
                'elargissement',
                'assignee',
                'echec_assignation',
            ])->default('en_attente_recherche');

            // 📊 Métadonnées de recherche
            $table->integer('rayon_recherche_km')
                ->default(2);

            $table->integer('tentatives')
                ->default(0);

            $table->timestamp('assigned_at')
                ->nullable();

            $table->timestamp('search_started_at')
                ->nullable();

            // 🔍 Index pour accélérer les recherches
            $table->index([
                'assignation_status',
                'created_at'
            ]);

            $table->index([
                'livreur_id',
                'status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Supprimer les clés étrangères
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['livreur_id']);

            // Supprimer les index
            $table->dropIndex([
                'assignation_status',
                'created_at'
            ]);

            $table->dropIndex([
                'livreur_id',
                'status'
            ]);

            // Supprimer les colonnes
            $table->dropColumn([
                'zone_id',
                'livreur_id',
                'distance_km',
                'tarif_base',
                'tarif_km_applied',
                'assignation_status',
                'rayon_recherche_km',
                'tentatives',
                'assigned_at',
                'search_started_at',
            ]);
        });
    }
};