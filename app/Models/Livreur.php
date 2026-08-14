<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livreur extends Model
{
    protected $fillable = [
        'user_id',
        'numero_plaque',
        'etat_moto',
        'experience',
        'zones_livraison',
        'photo_identite_path',
        'photo_piece_identite_path',
        'photo_moto_path',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'zones_livraison' => 'array', 
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un livreur peut avoir plusieurs commandes assignées
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'livreur_id');
    }
}
