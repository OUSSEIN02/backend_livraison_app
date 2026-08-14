<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDeliveryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
    
        $requests = OrderDeliveryRequest::where('livreur_id', $user->id)
            ->with('order.user')
            ->orderBy('created_at', 'desc')
            ->get();
    
        $courses = $requests->map(function ($request) {
            $order = $request->order;
            
            // Sécurité : ignorer si la commande a été supprimée
            if (!$order) return null;
    
            return [
                'id' => '#CMD-' . $order->id,
                'backend_id' => $order->id,
                'customer' => $order->user?->name ?? $order->pickup_name ?? 'Client',
                'pickup' => $order->pickup_address ?? '—',
                'dropoff' => $order->dropoff_address ?? '—',
                
                // 🆕 Coordonnées GPS
                'pickup_lat' => $order->pickup_lat ? (float)$order->pickup_lat : null,
                'pickup_lng' => $order->pickup_lng ? (float)$order->pickup_lng : null,
                'dropoff_lat' => $order->dropoff_lat ? (float)$order->dropoff_lat : null,
                'dropoff_lng' => $order->dropoff_lng ? (float)$order->dropoff_lng : null,
                
                'amount' => (int) ($order->tarif_total ?? $order->total_amount ?? 0),
                'date' => $order->created_at?->toIso8601String(),
                'distance' => $order->distance_km ? (number_format((float)$order->distance_km, 1) . ' km') : '0 km',
                'package_photo' => $order->package_photo ? asset('storage/' . $order->package_photo) : null,
                'duration' => '',
                'status' => $order->status ?? 'en_attente',
            ];
        })->filter(); // filter() enlève les valeurs null
        
        // ✅ Renvoie sous forme d'objet avec la clé "data" (Standard API)
        return response()->json([
            'data' => $courses->values() // values() réindexe le tableau proprement
        ]);
    }


    public function accept(Request $request, $requestId)
{
    $user = Auth::user();

    // On récupère le profil livreur de cet utilisateur
    $livreurProfile = $user->livreur; 

    if (!$livreurProfile) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun profil livreur associé à cet utilisateur.',
        ], 403);
    }

    try {
        $result = DB::transaction(function () use ($user, $livreurProfile, $requestId) {

            $deliveryRequest = OrderDeliveryRequest::where('order_id', $requestId)
                ->where('livreur_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$deliveryRequest) {
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Demande de livraison introuvable.',
                ];
            }

            if ($deliveryRequest->status !== 'pending') {
                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'Cette demande n’est plus disponible.',
                ];
            }

            if ($deliveryRequest->isExpired()) {
                $deliveryRequest->update(['status' => 'expired']);

                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'Cette demande a expiré.',
                ];
            }

            // Verrouille la commande
            $order = Order::where('id', $deliveryRequest->order_id)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Commande introuvable.',
                ];
            }

            // Vérifie si un autre livreur a déjà accepté
            if ($order->livreur_id !== null) {
                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'Un autre livreur a déjà accepté cette commande.',
                ];
            }

            // ✅ CORRECTION : Assigner l'ID du profil Livreur (table livreurs), pas l'ID User
            $order->update([
                'livreur_id' => $livreurProfile->id, 
                'status' => 'en_cours',
            ]);

            // Cette demande devient acceptée
            $deliveryRequest->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Les autres demandes deviennent indisponibles
            OrderDeliveryRequest::where('order_id', $order->id)
                ->where('id', '!=', $deliveryRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'refused',
                    'responded_at' => now(),
                ]);

            return [
                'success' => true,
                'status' => 200,
                'message' => 'Commande acceptée avec succès.',
                'order' => $order->fresh('livreur.user'),
            ];
        });

        return response()->json($result, $result['status']);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l’acceptation de la commande.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}