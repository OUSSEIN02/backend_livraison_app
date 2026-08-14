<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendeurController extends Controller
{
    /**
     * Liste de tous les vendeurs (pour l'admin)
     */
    public function index(Request $request)
    {
        // 1. Démarrer la requête de base avec les relations
        $query = Seller::with(['user:id,name,email,phone'])
                       ->withCount('orders');
    
        // 2. Appliquer les filtres dynamiques envoyés par le frontend React
        
        // Filtre de recherche (Nom de l'entreprise, SIRET, ou Nom/Email/Téléphone de l'utilisateur)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
    
        // Filtre par statut (actif, en_attente, suspendu)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
    
        // 3. Pagination (au lieu de ->get())
        $perPage = $request->get('per_page', 10); // Défaut à 10 si non spécifié
        $sellers = $query->orderBy('created_at', 'desc')->paginate($perPage);
    
        // 4. Formater les données pour le frontend
        // ⚠️ IMPORTANT : On utilise ->through() au lieu de ->map() 
        // car ->through() préserve les métadonnées de pagination (total, last_page, etc.)
        $formattedData = $sellers->through(function ($seller) {
            return [
                'id' => $seller->id,
                'user' => [
                    'name' => $seller->user->name ?? 'Inconnu',
                    'email' => $seller->user->email ?? 'N/A',
                    'phone' => $seller->user->phone ?? 'N/A',
                    
                ],
                'company_name' => $seller->company_name,
                'email' => $seller->email,
                'phone' => $seller->phone,
                'address' => $seller->address,
                'city' => $seller->city,
                'country' => $seller->country,
                'siret' => $seller->siret ?? 'Non renseigné',
                'status' => $seller->status,
                'selfie_path' => $seller->selfie_path ? asset('storage/' . $seller->selfie_path) : null,
                'orders_count' => $seller->orders_count ?? 0,
                'created_at' => $seller->created_at,
            ];
        });
    
        // 5. Retourner la réponse. 
        // Laravel formate automatiquement l'objet paginé en JSON avec 'data', 'total', 'last_page', etc.
        // Ce qui correspond EXACTEMENT à ce que votre code React attend.
        return response()->json($formattedData, 200);
    }
    /**
     * Détails d'un vendeur spécifique
     */
    public function show($id)
    {
        $seller = Seller::with(['user:id,name,email,phone'])
            ->withCount('orders')
            ->findOrFail($id);

        $responseData = [
            'id' => $seller->id,
            'user' => [
                'id' => $seller->user->id,
                'name' => $seller->user->name ?? 'Inconnu',
                'email' => $seller->user->email ?? 'N/A',
                'phone' => $seller->user->phone ?? 'N/A',
                'avatar' => $seller->user->avatar ? asset('storage/' . $seller->user->avatar) : null,
                'email_verified_at' => $seller->user->email_verified_at,
            ],
            'company_name' => $seller->company_name,
            'email' => $seller->email,
            'phone' => $seller->phone,
            'address' => $seller->address,
            'city' => $seller->city,
            'country' => $seller->country,
            'siret' => $seller->siret ?? null,
            'status' => $seller->status,
            'id_front_path' => $seller->id_front_path ? asset('storage/' . $seller->id_front_path) : null,
            'id_back_path' => $seller->id_back_path ? asset('storage/' . $seller->id_back_path) : null,
            'selfie_path' => $seller->selfie_path ? asset('storage/' . $seller->selfie_path) : null,
            'orders_count' => $seller->orders_count,
            'created_at' => $seller->created_at,
            'updated_at' => $seller->updated_at,
        ];

        return response()->json([
            'message' => 'Détails du vendeur récupérés avec succès.',
            'data' => $responseData
        ], 200);
    }

    /**
     * Valider un vendeur
     */
    public function validateVendeur($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->status = 'valide';
        $seller->save();

        return response()->json([
            'message' => 'Le vendeur a été validé avec succès.'
        ], 200);
    }

    /**
     * Suspendre un vendeur
     */
    public function suspendVendeur($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->status = 'suspendu';
        $seller->save();

        return response()->json([
            'message' => 'Le vendeur a été suspendu.'
        ], 200);
    }


     /**
     * Valider un LIVREUR
     */
    public function validateLivreur($id)
    {
        $seller = Livreur::findOrFail($id);
        $seller->status = 'valide';
        $seller->save();

        return response()->json([
            'message' => 'Le compte du livreur a été validé avec succès.'
        ], 200);
    }


    
    /**
     * Suspendre un Livreur
     */
    public function suspendLivreur($id)
    {
        $seller = Livreur::findOrFail($id);
        $seller->status = 'suspendu';
        $seller->save();

        return response()->json([
            'message' => 'Le compte livreur a été suspendu.'
        ], 200);
    }

    
}