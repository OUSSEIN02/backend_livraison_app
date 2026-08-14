<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'livreur_id', // Null au début, rempli lors du matching
        'pickup_name',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_name',
        'dropoff_address',
        'dropoff_lat',
        'dropoff_lng',
        'weight',
        'is_fragile',
        'declared_value',
        'delivery_type',
        'scheduled_date',
        'instructions',
        'package_photo',
        'status', // 'en_attente', 'assignee', 'en_cours', 'livree', 'annulee', 'litige'
        'total_amount', // Calculé plus tard par le système de tarification
        'zone_id', 'livreur_id', 'distance_km', 'tarif_total',
        'tarif_base', 'tarif_km_applied', 'assignation_status',
        'rayon_recherche_km', 'tentatives', 'assigned_at', 'search_started_at',
        'last_lat', 'last_lng', 'last_heading', 'last_speed', 'last_location_at',
    ];

    protected $casts = [
        'is_fragile' => 'boolean',
        'declared_value' => 'decimal:2',
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'dropoff_lat' => 'decimal:8',
        'dropoff_lng' => 'decimal:8',
        'scheduled_date' => 'datetime',
        'assigned_at' => 'datetime',
        'search_started_at' => 'datetime',
        'last_lat'        => 'decimal:8',
        'last_lng'        => 'decimal:8',
        'last_heading'    => 'decimal:2',
        'last_speed'      => 'decimal:2',
        'last_location_at' => 'datetime',
    ];

  

    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }
    // public function livreur(): BelongsTo { return $this->belongsTo(User::class, 'livreur_id'); }
    public function deliveryRequests(): HasMany { return $this->hasMany(OrderDeliveryRequest::class); }
    
    public function scopePendingAssignment($query)
    {
        return $query->whereIn('assignation_status', [
            'en_attente_recherche', 'recherche_en_cours', 'elargissement'
        ]);
    }
  
    // Relations
    public function vendeur()
    {
        return $this->belongsTo(Seller::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livreur()
    {
        return $this->belongsTo(Livreur::class);
    }

    

    public function locations(): HasMany
    {
        return $this->hasMany(DeliveryLocation::class)->orderBy('recorded_at', 'desc');
    }

    public function lastLocation(): HasOne
    {
        return $this->hasOne(DeliveryLocation::class)->latestOfMany('recorded_at');
    }
}