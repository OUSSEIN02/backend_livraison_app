<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDeliveryRequest extends Model
{
    protected $fillable = [
        'order_id', 'livreur_id', 'rayon_km', 'distance_au_livreur',
        'status', 'sent_at', 'responded_at', 'expires_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function livreur(): BelongsTo { return $this->belongsTo(User::class, 'livreur_id'); }
    
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }
}
