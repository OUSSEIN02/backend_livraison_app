<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryLocation extends Model
{
    protected $fillable = [
        'order_id',
        'livreur_id',
        'latitude',
        'longitude',
        'heading',
        'speed',
        'accuracy',
        'phase',
        'recorded_at',
    ];

    protected $casts = [
        'latitude'    => 'decimal:8',
        'longitude'   => 'decimal:8',
        'heading'     => 'decimal:2',
        'speed'       => 'decimal:2',
        'accuracy'    => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }
}