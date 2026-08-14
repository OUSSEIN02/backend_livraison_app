<?php

// app/Jobs/SearchLivreurJob.php
namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderDeliveryRequest;
use App\Models\User; // Livreurs
use App\Events\DeliveryRequestSent;
use App\Events\LivreurSearchExpanded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchLivreurJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes max par exécution
    public $tries = 5;

    private const TIMEOUT_SECONDS = 25; // 25s avant d'élargir
    private const MAX_RAYON_KM = 15;    // Rayon maximum de recherche
    private const RAYON_STEP_KM = 2;    // On élargit de 2km à chaque fois

    public function __construct(
        private Order $order,
        private int $currentRayon = 2
    ) {}

    public function handle(): void
    {
        // Sécurité : si la commande est déjà assignée ou annulée, on arrête
        $this->order->refresh();
        if ($this->order->assignation_status === 'assignee' 
            || $this->order->status === 'annulee') {
            return;
        }

        Log::info("🔍 Recherche livreur", [
            'order' => $this->order->id,
            'rayon' => $this->currentRayon . ' km'
        ]);

        // 1. Met à jour le statut
        $this->order->update([
            'assignation_status' => 'recherche_en_cours',
            'rayon_recherche_km' => $this->currentRayon,
            'tentatives' => $this->order->tentatives + 1,
        ]);

        // 2. Trouver les livreurs dans le rayon
        $livreurs = $this->findLivreursInRayon($this->currentRayon);

        if ($livreurs->isEmpty()) {
            Log::info("Aucun livreur trouvé dans rayon {$this->currentRayon}km");
            $this->expandOrCancel();
            return;
        }

        // 3. Envoie une notification à chaque livreur
        $expiresAt = now()->addSeconds(self::TIMEOUT_SECONDS);
        
        DB::transaction(function () use ($livreurs, $expiresAt) {
            foreach ($livreurs as $livreur) {
                OrderDeliveryRequest::create([
                    'order_id' => $this->order->id,
                    'livreur_id' => $livreur->id,
                    'rayon_km' => $this->currentRayon,
                    'distance_au_livreur' => $livreur->distance_km,
                    'status' => 'pending',
                    'sent_at' => now(),
                    'expires_at' => $expiresAt,
                ]);
            }
        });

        // 4. Broadcast aux livreurs (WebSocket/Pusher)
        broadcast(new DeliveryRequestSent($this->order, $livreurs));

        Log::info("📱 Notifications envoyées", [
            'order' => $this->order->id,
            'livreurs_count' => $livreurs->count()
        ]);

        // 5. Attendre 25s puis vérifier si quelqu'un a accepté
        sleep(self::TIMEOUT_SECONDS);

        // 6. Vérifier si la commande a été acceptée pendant le timeout
        $this->order->refresh();
        
        if ($this->order->assignation_status === 'assignee') {
            Log::info("✅ Livreur assigné !", ['order' => $this->order->id]);
            // Marquer les autres requêtes comme "expired"
            OrderDeliveryRequest::where('order_id', $this->order->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired', 'responded_at' => now()]);
            return;
        }

        // 7. Personne n'a accepté → élargir le rayon
        $this->expandOrCancel();
    }

    /**
     * Élargit le rayon ou abandonne.
     */
    private function expandOrCancel(): void
    {
        $nextRayon = $this->currentRayon + self::RAYON_STEP_KM;

        if ($nextRayon > self::MAX_RAYON_KM) {
            // Abandon
            $this->order->update([
                'assignation_status' => 'echec_assignation',
                'status' => 'echec_assignation',
            ]);
            
            Log::warning("❌ Aucun livreur trouvé pour la commande", [
                'order' => $this->order->id,
                'rayon_max' => self::MAX_RAYON_KM
            ]);
            
            // Notifier le vendeur
            broadcast(new \App\Events\LivreurSearchFailed($this->order));
            return;
        }

        // Notifier le vendeur qu'on élargit
        broadcast(new LivreurSearchExpanded($this->order, $nextRayon));

        // Relance le job avec le nouveau rayon
        self::dispatch($this->order, $nextRayon)->delay(now()->addSeconds(3));
    }

    /**
     * Trouve les livreurs en ligne dans le rayon, triés par distance.
     */
    private function findLivreursInRayon(int $rayonKm)
    {
        $pickupLat = (float) $this->order->pickup_lat;
        $pickupLng = (float) $this->order->pickup_lng;

        // Récupère les livreurs actifs
        $livreurs = User::where('role', 'livreur')
            ->where('status', 'actif')
            ->where('is_online', true)
            ->whereNotNull('last_lat')
            ->whereNotNull('last_lng')
            ->where('last_location_at', '>=', now()->subMinutes(15)) // en ligne récemment
            ->get();

        // Filtre par distance
        $distanceService = app(\App\Services\DistanceService::class);

        return $livreurs->map(function ($livreur) use ($pickupLat, $pickupLng, $distanceService) {
            $distance = $distanceService->calculate(
                $pickupLat, $pickupLng,
                (float) $livreur->last_lat,
                (float) $livreur->last_lng
            );
            $livreur->distance_km = round($distance, 2);
            return $livreur;
        })
        ->filter(fn($l) => $l->distance_km <= $rayonKm)
        ->sortBy('distance_km')
        ->take(10) // max 10 livreurs notifiés à la fois
        ->values();
    }
}