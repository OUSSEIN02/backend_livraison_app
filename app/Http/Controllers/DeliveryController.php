<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DeliveryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Reçoit et enregistre la position en temps réel du livreur.
     * 
     * Cette méthode est appelée très fréquemment (toutes les ~3s) par l'app Flutter.
     * Elle doit donc être :
     *  - Rapide
     *  - Robuste aux données invalides
     *  - Capable de gérer plusieurs envois simultanés
     */
    public function updateLocation(Request $request, $orderId)
    {
        // ─── 1. Validation des données ────────────────────────
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'heading'   => 'nullable|numeric|between:0,360',
            'speed'     => 'nullable|numeric|min:0',
            'accuracy'  => 'nullable|numeric|min:0',
            'timestamp' => 'nullable|date',
            'phase'     => 'required|string|in:start,toPickup,toDropoff,completed',
        ]);

        // ─── 2. Vérifier que la commande existe et appartient au livreur ─────
        $livreur = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('livreur_id', $livreur->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable ou non autorisée',
            ], 404);
        }

        // ─── 3. Vérifier que la commande est en cours (pas encore livrée/annulée) ─
        $allowedStatuses = ['en_cours', 'assignee', 'en_cours_livraison'];
        if (!in_array($order->status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'La commande n\'est plus en cours de livraison',
            ], 422);
        }

        // ─── 4. Enregistrer dans l'historique ────────────────
        try {
            $recordedAt = isset($validated['timestamp'])
                ? \Carbon\Carbon::parse($validated['timestamp'])
                : now();

            $location = DeliveryLocation::create([
                'order_id'    => $order->id,
                'livreur_id'  => $livreur->id,
                'latitude'    => $validated['latitude'],
                'longitude'   => $validated['longitude'],
                'heading'     => $validated['heading'] ?? 0,
                'speed'       => $validated['speed'] ?? 0,
                'accuracy'    => $validated['accuracy'] ?? 0,
                'phase'       => $validated['phase'],
                'recorded_at' => $recordedAt,
            ]);

            // ─── 5. Mettre à jour la dernière position sur la commande ─────
            // (accès rapide pour afficher sur la carte client/vendeur)
            $order->update([
                'last_lat'        => $validated['latitude'],
                'last_lng'        => $validated['longitude'],
                'last_heading'    => $validated['heading'] ?? 0,
                'last_speed'      => $validated['speed'] ?? 0,
                'last_location_at' => $recordedAt,
            ]);

            // ─── 6. (Optionnel) Détection automatique d'arrivée ─────
            // Si le livreur est très proche du point de destination, on peut
            // automatiquement suggérer de confirmer l'arrivée.
            $autoArrival = $this->checkAutoArrival($order, $validated, $location);

            return response()->json([
                'success' => true,
                'message' => 'Position enregistrée',
                'location_id' => $location->id,
                'auto_arrival' => $autoArrival, // true si proche du but
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur enregistrement position livraison', [
                'order_id'   => $orderId,
                'livreur_id' => $livreur->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
            ], 500);
        }
    }

    /**
     * Vérifie si le livreur est arrivé à moins de 50m du point cible.
     * Utile pour notifier automatiquement au livreur de confirmer.
     */
    private function checkAutoArrival(Order $order, array $data, DeliveryLocation $location): bool
    {
        $targetLat = null;
        $targetLng = null;

        if ($data['phase'] === 'toPickup') {
            $targetLat = $order->pickup_lat;
            $targetLng = $order->pickup_lng;
        } elseif ($data['phase'] === 'toDropoff') {
            $targetLat = $order->dropoff_lat;
            $targetLng = $order->dropoff_lng;
        }

        if (!$targetLat || !$targetLng) return false;

        // Calcul distance Haversine
        $distance = $this->haversineDistance(
            $data['latitude'], $data['longitude'],
            $targetLat, $targetLng
        );

        return $distance <= 50; // 50 mètres
    }

    /**
     * Calcule la distance en mètres entre deux coordonnées GPS (Haversine)
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $R = 6371000; // Rayon de la Terre en mètres
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1);
        $dlambda = deg2rad($lng2 - $lng1);

        $a = sin($dphi / 2) ** 2 +
             cos($phi1) * cos($phi2) * sin($dlambda / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    /**
     * (Bonus) Récupérer l'historique complet des positions d'une commande.
     * Utile pour afficher le trajet parcouru sur une carte.
     */
    public function getTrack(Request $request, $orderId)
    {
        $livreur = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('livreur_id', $livreur->id)
            ->firstOrFail();

        $locations = DeliveryLocation::where('order_id', $order->id)
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'heading', 'speed', 'phase', 'recorded_at']);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'track' => $locations,
            'total_points' => $locations->count(),
        ]);
    }
}