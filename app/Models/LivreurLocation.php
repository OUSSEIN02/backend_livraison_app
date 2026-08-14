<?php

// app/Models/LivreurLocation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivreurLocation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'accuracy',
        'status',
        'last_seen_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcule la distance entre cette position et un point donné (formule Haversine)
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $R = 6371; // Rayon de la Terre en km
        
        $lat1 = deg2rad($this->latitude);
        $lat2 = deg2rad($lat);
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $R * $c; // Distance en km
    }
}