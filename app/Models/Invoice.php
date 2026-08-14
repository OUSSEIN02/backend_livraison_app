<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'order_id',
        'amount',
        'status',
        'issued_at',
        'pdf_path',
    ];

    /**
     * Les attributs qui doivent être convertis (casts).
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    /**
     * Relation : Une facture appartient à un utilisateur (Vendeur).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : Une facture appartient à une commande.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accesseur (Optionnel) : Pour formater le montant avec la devise si besoin en backend
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }
}