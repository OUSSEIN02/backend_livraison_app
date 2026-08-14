<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AttributionController extends Controller
{
    /**
     * 1. Récupérer les commandes en attente d'attribution manuelle
     */
    public function pendingAttribution(Request $request)
    {
        // On récupère les commandes sans livreur ou avec un statut 'en_attente'
        // Triées par date de création ASC (les plus anciennes/urgentes en premier)
        $orders = Order::where(function ($query) {
            $query->whereNull('livreur_id')
                  ->orWhere('status', 'en_attente');
        })
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'reference' => $order->reference ?? "CMD-{$order->id}",
                'dropoff_name' => $order->dropoff_name ?? 'Client inconnu',
                'pickup_address' => $order->pickup_address ?? 'Adresse inconnue',
                'dropoff_address' => $order->dropoff_address ?? 'Adresse inconnue',
                'pickup_city' => $order->pickup_city ?? '', // Utilisé pour matcher la zone du livreur
                'total_amount' => $order->total_amount ?? 0,
                'distance_km' => $order->distance_km ?? null,
                // Calcul dynamique du temps d'attente en minutes
                'wait_time_minutes' => Carbon::parse($order->created_at)->diffInMinutes(now()),
                'created_at' => $order->created_at,
            ];
        });

        return response()->json([
            'message' => 'Commandes en attente récupérées.',
            'data' => $orders
        ], 200);
    }

    /**
     * 2. Assigner manuellement un livreur à une commande
     */
    public function assignLivreur(Request $request, $orderId)
    {
        $request->validate([
            'livreur_id' => 'required|exists:livreurs,id'
        ]);

        $order = Order::findOrFail($orderId);

        // Vérification de sécurité : on n'assigne que si la commande est éligible
        if (!in_array($order->status, ['en_attente', 'annulee'])) { 
            // Ajoutez les statuts qui permettent l'assignation selon votre logique
            return response()->json([
                'message' => 'Cette commande ne peut plus être assignée (statut actuel : ' . $order->status . ').'
            ], 400);
        }

        try {
            DB::transaction(function () use ($order, $request) {
                $order->update([
                    'livreur_id' => $request->livreur_id,
                    'status' => 'assignee', // Ou 'en_cours' selon votre workflow
                    'assigned_at' => now(), // Bonne pratique pour tracer l'assignation
                ]);
            });

            // Récupérer le nom du livreur pour le message de succès
            $livreur = Livreur::with('user:id,name')->find($request->livreur_id);
            $livreurName = $livreur->user->name ?? 'Le livreur';

            return response()->json([
                'message' => "La commande a été attribuée avec succès à {$livreurName}."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'attribution.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function disponibles(Request $request)
    {
        // On prend les livreurs validés/actifs
        $drivers = Livreur::where('status', 'valide') // ou 'actif' selon votre BDD
            ->with(['user:id,name'])
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'user' => [
                        'name' => $driver->user->name ?? 'Inconnu',
                    ],
                    // Le cast 'array' dans le modèle Livreur est OBLIGATOIRE ici
                    'zones_livraison' => $driver->zones_livraison ?? [], 
                    'city' => $driver->city ?? 'Non définie',
                    'note_moyenne' => $driver->note_moyenne ?? 0,
                    'taux_acceptation' => $driver->taux_acceptation ?? 0,
                    // Comptage dynamique des courses en cours pour ce livreur
                    'current_orders_count' => $driver->orders()
                        ->whereIn('status', ['assignee', 'en_cours'])
                        ->count(),
                ];
            });

        return response()->json([
            'message' => 'Livreurs disponibles récupérés.',
            'data' => $drivers
        ], 200);
    }
}