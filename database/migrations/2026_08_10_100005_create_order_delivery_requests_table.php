<?php
// database/migrations/xxxx_create_order_delivery_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_delivery_requests', function (Blueprint $table) {
            $table->id();
            
            // 🔗 Lien vers la commande
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade'); // Si la commande est supprimée, les requêtes aussi
            
            // 🔗 Lien vers le livreur (user avec role = 'livreur')
            $table->foreignId('livreur_id')
                  ->constrained('livreurs') // ou 'livreurs' si vous avez une table séparée
                  ->onDelete('cascade');
            
            // 📍 Contexte de la notification
            $table->integer('rayon_km'); // Rayon de recherche au moment de l'envoi (ex: 2, 4, 6...)
            $table->decimal('distance_au_livreur', 8, 2); // Distance réelle entre livreur et pickup (km)
            
            // 📊 État de la requête
            $table->enum('status', [
                'pending',   // Envoyée, en attente de réponse
                'accepted',  // Le livreur a accepté
                'refused',   // Le livreur a refusé
                'expired'    // Timeout dépassé, pas de réponse
            ])->default('pending');
            
            // ⏰ Timestamps du cycle de vie
            $table->timestamp('sent_at');      // Quand la notification a été envoyée
            $table->timestamp('responded_at')->nullable();  // Quand le livreur a répondu
            $table->timestamp('expires_at');   // Quand cette requête expire (sent_at + 25s)
            
            $table->timestamps();
            
            // 🔍 Index pour optimiser les requêtes fréquentes
            $table->index(['order_id', 'status']);
            $table->index(['livreur_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_delivery_requests');
    }
};