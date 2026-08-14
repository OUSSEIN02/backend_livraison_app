<?php

namespace App\Http\Controllers;

use App\Models\Litige; // Assurez-vous d'avoir ce modèle
use Illuminate\Http\Request;
use Carbon\Carbon;

class LitigeController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les litiges avec leurs relations
        $litiges = Litige::with([
            'order:id,reference,total_amount', 
            'seller:id,company_name', 
            'livreur:id,nom' // Adaptez 'livreur' ou 'driver' selon votre nom de relation
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($litige) {
            return [
                'id' => $litige->id,
                'order_id' => $litige->order_id,
                'order_reference' => $litige->order->reference ?? 'N/A',
                'seller_name' => $litige->seller->company_name ?? 'Vendeur inconnu',
                'driver_name' => $litige->livreur->nom ?? 'Non assigné',
                'status' => $litige->status, // en_attente, en_cours, resolu
                'type' => $litige->type, // ex: Colis non reçu, Produit endommagé
                'description' => $litige->description,
                'date_formatted' => Carbon::parse($litige->created_at)->format('d/m/Y'),
                'montant_formatted' => $litige->order ? number_format($litige->order->total_amount, 0, ',', ' ') . ' FCFA' : 'N/A',
                'priority' => $litige->priorite ?? 'moyenne', // haute, moyenne, basse
            ];
        });

        return response()->json([
            'message' => 'Liste des litiges récupérée avec succès.',
            'data' => $litiges
        ], 200);
    }
}