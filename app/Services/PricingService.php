<?php
// app/Services/PricingService.php
namespace App\Services;

use App\Models\Zone;

class PricingService
{
    public function __construct(private DistanceService $distanceService) {}
    
    /**
     * Calcule le tarif total d'une commande.
     * @return array{distance_km: float, tarif_total: float, tarif_base: float, tarif_km: float, zone: Zone|null}
     */
    public function calculate(
        float $pickupLat, float $pickupLng,
        float $dropoffLat, float $dropoffLng
    ): array {
        $distance = $this->distanceService->calculate($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
        
        // Zone du point de récupération
        $zone = $this->distanceService->findZone($pickupLat, $pickupLng);
        
        if (!$zone || $zone->statut !== 'actif') {
            // Pas de zone → tarif par défaut
            return [
                'distance_km' => round($distance, 2),
                'tarif_total' => 1500, // tarif minimum hors zone
                'tarif_base' => 1000,
                'tarif_km' => 200,
                'zone' => null,
            ];
        }
        
        $tarifKm = (float) $zone->tarif_km;
        $total = $tarifKm * $distance;
        
        // Tarif minimum
        $total = max($total, 500);
        
        return [
            'distance_km' => round($distance, 2),
            'tarif_total' => round($total, 2),
            'tarif_base' => 0,
            'tarif_km' => $tarifKm,
            'zone' => $zone,
        ];
    }
}