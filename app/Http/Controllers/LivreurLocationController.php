<?php

namespace App\Http\Controllers;

use App\Models\LivreurLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderDeliveryRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;




class LivreurLocationController extends Controller
{
    /**
     * Met à jour la position du livreur (polling)
     * POST /api/livreur/location
     */
    public function updateLocation(Request $request)
    {

        $user = $request->user();
        $role = $user->role ?? null;

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'accuracy' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $location = LivreurLocation::updateOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'speed' => $request->speed,
                    'heading' => $request->heading,
                    'accuracy' => $request->accuracy,
                    'status' => $request->status ?? 'available',
                    'last_seen_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Position mise à jour',
                'data' => $location,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère les livreurs disponibles dans un rayon donné
     * GET /api/livreur/nearby?lat=X&lng=Y&radius=Z
     */



     public function getNearbyLivreurs(Request $request)
{
    $validator = Validator::make($request->all(), [
        'order_id' => 'required|integer|exists:orders,id',
        'lat'      => 'required|numeric',
        'lng'      => 'required|numeric',
        'radius'   => 'required|numeric|min:0.5|max:10',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors(),
        ], 422);
    }

    $orderId = (int) $request->order_id;
    $lat     = (float) $request->lat;
    $lng     = (float) $request->lng;
    $radius  = (float) $request->radius;

    /*
    |--------------------------------------------------------------------------
    | 1. Récupérer la commande
    |--------------------------------------------------------------------------
    */
    $order = Order::with([
        'livreur:id,user_id,photo_identite_path',
        'livreur.user:id,name,phone',
    ])->find($orderId);

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Commande introuvable.',
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Vérifier si un livreur a déjà été attribué
    |--------------------------------------------------------------------------
    */
    if ($order->livreur_id) {
        /** @var \App\Models\Livreur|null $livreur */
        $livreur = $order->livreur;

        // Si le livreur existe bien en BDD
        if ($livreur) {
            $location = LivreurLocation::where('user_id', $livreur->user_id)->first();
            $user     = $livreur->user;

            return response()->json([
                'success'   => true,
                'assigned'  => true,
                'message'   => 'Un livreur a déjà été attribué à cette commande.',
                'count'     => 1,
                'radius_km' => $radius,
                'data'      => [
                    [
                        'id'        => $livreur->id,
                        'name'      => $user?->name ?? 'Livreur',
                        'phone'     => $user?->phone ?? '',
                        'photo'     => $livreur->photo_identite_path
                            ? asset('storage/' . $livreur->photo_identite_path)
                            : 'https://i.pravatar.cc/150?u=' . $livreur->id,
                        'rating'    => (float) ($livreur->rating ?? 4.5),
                        'distance'  => $location ? round($location->distanceTo($lat, $lng), 2) : null,
                        'latitude'  => $location ? (float) $location->latitude : null,
                        'longitude' => $location ? (float) $location->longitude : null,
                        'speed'     => $location ? (float) ($location->speed ?? 0) : 0,
                        'vehicule'  => $livreur->vehicule ?? 'Moto',
                        'heading'   => $location ? (float) ($location->heading ?? 0) : 0,
                        'last_seen' => $location?->last_seen_at ? $location->last_seen_at->diffForHumans() : null,
                        'status'    => 'assigned',
                    ],
                ],
            ]);
        }
        
        // Sécurité : si livreur_id n'est pas null mais que l'enregistrement n'existe pas en BDD,
        // on ignore le bloc et on continue vers la recherche normale.
        Log::warning("Order ID {$orderId} a un livreur_id ({$order->livreur_id}) introuvable dans la table livreurs.");
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Recherche des livreurs disponibles aux alentours
    |--------------------------------------------------------------------------
    */
    $threshold = now()->subMinutes(5);

    $locations = LivreurLocation::where('status', 'available')
        ->where('last_seen_at', '>=', $threshold)
        ->with([
            'user:id,name,phone',
            'user.livreur:id,user_id,photo_identite_path',
        ])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 4. Filtrage et calcul des distances
    |--------------------------------------------------------------------------
    */
    $nearbyLivreurs = $locations
        ->map(function ($location) use ($lat, $lng) {
            $distance = $location->distanceTo($lat, $lng);
            $user     = $location->user;
            $livreur  = $user?->livreur;

            return [
                'id'        => $location->user_id, // ID User utilisé pour OrderDeliveryRequest
                'name'      => $user?->name ?? 'Livreur',
                'phone'     => $user?->phone ?? '',
                'photo'     => $livreur?->photo_identite_path
                    ? asset('storage/' . $livreur->photo_identite_path)
                    : 'https://i.pravatar.cc/150?u=' . $location->user_id,
                'rating'    => (float) ($user?->rating ?? 4.5),
                'distance'  => round($distance, 2),
                'latitude'  => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'speed'     => (float) ($location->speed ?? 0),
                'vehicule'  => $user?->vehicule ?? 'Moto',
                'heading'   => (float) ($location->heading ?? 0),
                'last_seen' => $location->last_seen_at?->diffForHumans(),
                'status'    => $location->status,
            ];
        })
        ->filter(fn($livreur) => $livreur['distance'] <= $radius)
        ->sortBy('distance')
        ->values()
        ->take(10);

    /*
    |--------------------------------------------------------------------------
    | 5. Créer les demandes de livraison
    |--------------------------------------------------------------------------
    */
    foreach ($nearbyLivreurs as $livreur) {
        $existingRequest = OrderDeliveryRequest::where('order_id', $orderId)
            ->where('livreur_id', $livreur['id'])
            ->first();

        if ($existingRequest) {
            continue;
        }

        OrderDeliveryRequest::create([
            'order_id'            => $orderId,
            'livreur_id'          => $livreur['id'],
            'rayon_km'            => $radius,
            'distance_au_livreur' => $livreur['distance'],
            'status'              => 'pending',
            'sent_at'             => now(),
            'expires_at'          => now()->addMinutes(2),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Réponse JSON
    |--------------------------------------------------------------------------
    */
    return response()->json([
        'success'   => true,
        'assigned'  => false,
        'message'   => $nearbyLivreurs->isEmpty()
            ? 'Aucun livreur disponible dans ce rayon.'
            : 'Livreurs disponibles.',
        'count'     => $nearbyLivreurs->count(),
        'radius_km' => $radius,
        'data'      => $nearbyLivreurs,
    ]);
}
    /**
     * Nettoie les anciennes positions (command artisan)
     */
    public function cleanup()
    {
        $threshold = now()->subMinutes(30);
        
        $deleted = LivreurLocation::where('last_seen_at', '<', $threshold)->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }





    

}