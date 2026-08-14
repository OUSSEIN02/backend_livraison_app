<?php
// app/Services/DistanceService.php
namespace App\Services;

class DistanceService
{
    /**
     * Calcule la distance en km entre 2 points GPS (formule Haversine).
     */
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        
        $a = sin($latDelta / 2) ** 2 
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Détermine dans quelle zone se trouvent des coordonnées.
     * Stratégie : on cherche la zone la plus proche (centre de la ville).
     * À terme, vous pourriez stocker les polygones de chaque zone.
     */
    public function findZone(float $lat, float $lng): ?Zone
    {
        // Pour chaque zone, on a un "centre" (ex: Libreville centre)
        // On retourne la zone dont le centre est le plus proche
        $centresZones = [
            'Estuaire (Libreville, Owendo, Akanda)' => [0.3920, 9.4536],
            'Haut-Ogooué (Franceville)' => [-1.6333, 13.5833],
            'Moyen-Ogooué (Lambaréné)' => [-0.7000, 10.2333],
            'Ngounié (Mouila)' => [-1.6167, 11.0500],
            'Nyanga (Tchibanga)' => [-2.8833, 11.0333],
            'Ogooué-Ivindo (Makokou)' => [0.5667, 12.8667],
            'Ogooué-Lolo (Koulamoutou)' => [-1.1333, 12.4667],
            'Ogooué-Maritime (Port-Gentil)' => [-0.7167, 8.7833],
            'Woleu-Ntem (Oyem)' => [1.6167, 11.5833],
        ];
        
        $closestZone = null;
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($centresZones as $nom => [$cLat, $cLng]) {
            $distance = $this->calculate($lat, $lng, $cLat, $cLng);
            if ($distance < $minDistance && $distance < 50) { // max 50km
                $minDistance = $distance;
                $closestZone = Zone::where('nom', $nom)->first();
            }
        }
        
        return $closestZone;
    }
}