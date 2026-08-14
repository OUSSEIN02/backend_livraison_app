<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Litige extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis en masse.
     */
    protected $fillable = [
        'order_id',
        'seller_id',
        'livreur_id',
        'type',
        'description',
        'status',
        'priorite',
    ];

    /**
     * Les attributs qui doivent être convertis (casts).
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation : Un litige appartient à une commande
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relation : Un litige appartient à un vendeur
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Relation : Un litige peut être associé à un livreur
     */
    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class, 'livreur_id');
    }

    // --- Scopes utilitaires (optionnels mais recommandés) ---

    /**
     * Scope pour filtrer uniquement les litiges en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', 'en_attente');
    }

    /**
     * Scope pour filtrer par priorité haute
     */
    public function scopeHautePriorite($query)
    {
        return $query->where('priorite', 'haute');
    }
}