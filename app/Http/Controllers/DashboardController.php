<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Livreur;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  
    public function overview()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

       
        $isRestrictedRole = $user->roles()
                                ->whereIn('slug', ['vendeur', 'livreur'])
                                ->exists();

        // 2. Préparer la requête de base pour Order
        $query = Order::query();

        // 3. Filtrer uniquement SI c'est un vendeur ou livreur
        if ($isRestrictedRole) {
            $query->where('user_id', $user->id);
        }

        // 4. Calcul des statistiques en réutilisant la requête filtrée ou non
        $stats = [
            'total'      => (clone $query)->count(),
            'en_attente' => (clone $query)->where('status', 'en_attente')->count(),
            'en_cours'   => (clone $query)->whereIn('status', ['en_cours', 'assignee'])->count(),
            'livree'     => (clone $query)->where('status', 'livree')->count(),
        ];

        // 5. Récupération des 5 dernières commandes
        $recentOrders = (clone $query)
            ->with('livreur:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'dropoff_name', 'total_amount', 'status', 'created_at']);

        return response()->json([
            'message' => 'Données du tableau de bord récupérées.',
            'data' => [
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'is_global_view' => !$isRestrictedRole // Indique au React si l'utilisateur voit TOUTES les commandes ou juste les siennes
            ]
        ], 200);
    }


  
    public function index(Request $request)
    {
        // 1. Démarrer la requête avec les relations nécessaires
        $query = Livreur::with(['user:id,name,email,phone,avatar'])
                        ->withCount('orders'); // Compte les livraisons

        // 2. Appliquer les filtres dynamiques envoyés par le frontend React
        
        // Filtre de recherche (Nom, Email ou Téléphone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtre par statut (en_attente, valide, suspendu, etc.)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtre par zone (recherche une valeur spécifique dans le tableau JSON)
        if ($request->filled('zone') && $request->zone !== 'all') {
            $query->whereJsonContains('zones_livraison', $request->zone);
        }

        // 3. Pagination (au lieu de ->get())
        $perPage = $request->get('per_page', 10); // Défaut à 10 si non spécifié
        $livreurs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // 4. Formater les données pour inclure les URLs complètes des images
        // 'through' permet de transformer les éléments tout en conservant la structure de pagination de Laravel
        $formattedData = $livreurs->through(function ($livreur) {
            return [
                'id' => $livreur->id,
                'user' => [
                    'name' => $livreur->user->name ?? 'Inconnu',
                    'email' => $livreur->user->email ?? 'N/A',
                    'phone' => $livreur->user->phone ?? 'N/A',
                    'avatar' => $livreur->user->avatar ? asset('storage/' . $livreur->user->avatar) : null,
                ],
                'numero_plaque' => $livreur->numero_plaque,
                'etat_moto' => $livreur->etat_moto,
                'zones_livraison' => $livreur->zones_livraison, // Déjà un tableau grâce au cast 'array'
                'status' => $livreur->status,
                'disponibilite' => $livreur->disponibilite ?? 'indisponible', // Ajouté pour correspondre au frontend
                'photo_identite_path' => $livreur->photo_identite_path ? asset('storage/' . $livreur->photo_identite_path) : null,
                'livraisons_count' => $livreur->orders_count ?? 0,
            ];
        });

        // 5. Retourner la réponse. 
        // Laravel formate automatiquement l'objet paginé en JSON avec 'data', 'total', 'last_page', etc.
        // Ce qui correspond EXACTEMENT à ce que votre code React attend.
        return response()->json($formattedData, 200);
    }


    public function showLivreurDetails($id)
    {
        // On charge le livreur avec la relation user, et on calcule les stats si elles existent
        $livreur = Livreur::with(['user:id,name,email,phone'])
            ->withCount('orders') // Si tu as la relation 'orders' définie dans le modèle Livreur
            ->findOrFail($id);

        // Formatage des données pour correspondre exactement aux attentes du frontend
        $responseData = [
            'id' => $livreur->id,
            'user' => [
                'name' => $livreur->user->name ?? 'Inconnu',
                'email' => $livreur->user->email ?? 'N/A',
                'phone' => $livreur->user->phone ?? 'N/A',
            ],
            'numero_plaque' => $livreur->numero_plaque,
            'etat_moto' => $livreur->etat_moto,
            'zones_livraison' => $livreur->zones_livraison, // Sera un tableau grâce au cast 'array'
            'status' => $livreur->status,
            'photo_identite_path' => $livreur->photo_identite_path ? asset('storage/' . $livreur->photo_identite_path) : null,
            'photo_piece_identite_url' => $livreur->photo_piece_identite_path ? asset('storage/' . $livreur->photo_piece_identite_path) : null,
            'photo_moto_path' => $livreur->photo_moto_path ? asset('storage/' . $livreur->photo_moto_path) : null,
            'created_at' => $livreur->created_at,
            // Stats (à adapter selon comment tu les calcules dans ta BDD)
            'livraisons_count' => $livreur->orders_count ?? 0,
            'note_moyenne' => $livreur->note_moyenne ?? null, 
            'temps_moyen' => $livreur->temps_moyen ?? null,
            'taux_acceptation' => $livreur->taux_acceptation ?? null,
        ];

        return response()->json([
            'message' => 'Détails du livreur récupérés avec succès.',
            'data' => $responseData
        ], 200);
    }


    // public function show($id)
    // {
    //     $livreur = Livreur::with(['user:id,name,email,phone'])
    //         ->withCount('orders')
    //         ->findOrFail($id);

    //     // Récupération des stats si disponibles
    //     $stats = [
    //         'livraisons_count' => $livreur->orders_count ?? 0,
    //         'note_moyenne' => $livreur->note_moyenne ?? null,
    //         'temps_moyen' => $livreur->temps_moyen ?? null,
    //         'taux_acceptation' => $livreur->taux_acceptation ?? null,
    //     ];

    //     $responseData = [
    //         'id' => $livreur->id,
    //         'user' => [
    //             'id' => $livreur->user->id,
    //             'name' => $livreur->user->name ?? 'Inconnu',
    //             'email' => $livreur->user->email ?? 'N/A',
    //             'phone' => $livreur->user->phone ?? 'N/A',
    //         ],
    //         'numero_plaque' => $livreur->numero_plaque,
    //         'etat_moto' => $livreur->etat_moto,
    //         'experience' => $livreur->experience,
    //         'zones_livraison' => $livreur->zones_livraison,
    //         'status' => $livreur->status,
            
    //         // URLs complètes des images pour affichage
    //         'photo_identite_url' => $livreur->photo_identite_path ? asset('storage/' . $livreur->photo_identite_path) : null,
    //         'photo_piece_identite_url' => $livreur->photo_piece_identite_path ? asset('storage/' . $livreur->photo_piece_identite_path) : null,
    //         'photo_moto_url' => $livreur->photo_moto_path ? asset('storage/' . $livreur->photo_moto_path) : null,
            
    //         // Stats
    //         'livraisons_count' => $stats['livraisons_count'],
    //         'note_moyenne' => $stats['note_moyenne'],
    //         'temps_moyen' => $stats['temps_moyen'],
    //         'taux_acceptation' => $stats['taux_acceptation'],
            
    //         'created_at' => $livreur->created_at,
    //         'updated_at' => $livreur->updated_at,
    //     ];

    //     return response()->json([
    //         'message' => 'Détails du livreur récupérés avec succès.',
    //         'data' => $responseData
    //     ], 200);
    // }

    /**
     * Valider le compte d'un livreur
     */
    public function validateLivreur($id)
    {
        $livreur = Livreur::findOrFail($id);
        $livreur->status = 'valide'; // ou 'actif'
        $livreur->save();

        return response()->json([
            'message' => 'Le livreur a été validé avec succès.'
        ], 200);
    }

    /**
     * Rejeter le compte d'un livreur
     */
    public function rejectLivreur($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $livreur = Livreur::findOrFail($id);
        $livreur->status = 'rejete';
        $livreur->save();

        // Optionnel : Envoyer un email au livreur pour l'informer

        return response()->json([
            'message' => 'Le livreur a été rejeté.'
        ], 200);
    }





    public function getNewCouriersCount()
    {
        // Compte tous les livreurs dont le statut est 'en_attente'
        // (Vous pouvez affiner avec un champ 'is_read' = false si vous préférez)
        $count = \App\Models\Livreur::where('status', 'en_attente')->count();

        return response()->json([
            'count' => $count
        ]);
    }


    public function getNewSellersCount()
    {
        // Compte tous les vendeurs dont le statut est 'en_attente'
        $count = \App\Models\Seller::where('status', 'en_attente')->count();

        return response()->json([
            'count' => $count
        ]);
    }
}