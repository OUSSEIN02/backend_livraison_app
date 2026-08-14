<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'email',
        'phone',
        'country',
        'city',
        'address',
        'password',
        'id_front_path',
        'id_back_path',
        'selfie_path',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un vendeur peut avoir plusieurs commandes
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
