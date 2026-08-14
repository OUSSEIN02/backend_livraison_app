<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'tarif_km',
        'statut'
    ];

    protected $casts = [
        'tarif_km' => 'decimal:2'
    ];

    // Relation optionnelle avec les livreurs/vendeurs
    // public function livreurs()
    // {
    //     return $this->belongsToMany(Livreur::class, 'livreur_zone');
    // }
}