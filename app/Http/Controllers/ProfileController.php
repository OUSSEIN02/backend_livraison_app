<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Récupérer le profil complet de l'utilisateur
     */
    public function show(Request $request)
    {
        // On charge l'utilisateur avec ses relations
        $user = $request->user()->load(['roles', 'vendeur']);

        // Déterminer le rôle principal (ex: 'vendeur', 'livreur', 'client')
        $roleName = $user->roles->isNotEmpty() 
            ? $user->roles->first()->name 
            : ($user->role ?? 'client');

        // Récupérer les infos spécifiques si c'est un vendeur
        $seller = $user->vendeur;
        
        // Construction d'une réponse propre et fusionnée
        $responseData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // Priorité au phone/address du vendeur s'il existe, sinon celui de l'user
            'phone' => $user->phone ?? ($seller ? $seller->phone : null),
            'address' => $user->address ?? ($seller ? $seller->address : null),
            'city' => $seller ? $seller->city : null,
            'country' => $seller ? $seller->country : null,
            'company_name' => $seller ? $seller->company_name : null,
            'role' => $roleName,
            // Génération de l'URL complète pour l'avatar si il existe
            'avatar' => $seller->selfie_path ? asset('storage/' . $seller->selfie_path) : null,
            'is_verified' => $seller->status === 'valide' ? 1 : 0,
            'is_seller' => $seller->status === 'valide' ? 1 : 0,
            'created_at' => $user->created_at,
        ];

        return response()->json([
            'message' => 'Profil récupéré',
            'data' => $responseData
        ]);
    }


     /**
     * Récupérer le profil complet de l'utilisateur
     */
    public function show2(Request $request)
    {
        // On charge l'utilisateur avec ses relations
        $user = $request->user()->load(['roles', 'livreur']);
    
        $roleName = $user->roles->isNotEmpty() 
            ? $user->roles->first()->name 
            : ($user->role ?? 'client');
    
        // Récupérer les infos spécifiques selon le rôle
        $livreur = $user->livreur;
        
        // ═══════════════════════════════════════════════════════
        // 📊 STATS LIVREUR (calculées dynamiquement)
        // ═══════════════════════════════════════════════════════
        $totalLivraisons = 0;
        $noteMoyenne = 0;
        $tauxAcceptation = 0;
    
        if ($livreur) {
            // Total des livraisons terminées
            $totalLivraisons = \App\Models\Order::where('livreur_id', $livreur->id)
                ->where('status', 'livree')
                ->count();
    
            
    
            // Taux d'acceptation = (commandes acceptées / commandes proposées)
            $commandesProposees = \App\Models\Order::where('livreur_id', $livreur->id)->count();
            $commandesAcceptees = \App\Models\Order::where('livreur_id', $livreur->id)
                ->whereIn('status', ['en_cours', 'livree'])
                ->count();
            
            $tauxAcceptation = $commandesProposees > 0 
                ? round(($commandesAcceptees / $commandesProposees) * 100) 
                : 100;
        }
    
        // ═══════════════════════════════════════════════════════
        // 🚚 Construction de la réponse LIVREUR
        // ═══════════════════════════════════════════════════════
        if ($livreur) {
            $responseData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'city' => $user->city ?? null,
                'country' => $user->country ?? null,
                'role' => $roleName,
                'avatar' => $user->avatar 
                    ? asset('storage/' . $user->avatar) 
                    : ($livreur->photo_identite_path 
                        ? asset('storage/' . $livreur->photo_identite_path) 
                        : null),
                'is_verified' => $livreur->status === 'valide' ? 1 : 0,
                'is_livreur' => 1,
                'created_at' => $user->created_at,
                
                // 📈 Stats
                'total_livraisons' => $totalLivraisons,
                'note_moyenne' => $noteMoyenne,
                'taux_acceptation' => $tauxAcceptation,
    
                // 🚚 Données spécifiques livreur
                'livreur' => [
                    'id' => $livreur->id,
                    'numero_plaque' => $livreur->numero_plaque,
                    'etat_moto' => $livreur->etat_moto,
                    'experience' => $livreur->experience,
                    'zones_livraison' => $livreur->zones_livraison,
                    'status' => $livreur->status,
                    
                    // 📄 Documents (KYC)
                    'photo_identite_path' => $livreur->photo_identite_path 
                        ? asset('storage/' . $livreur->photo_identite_path) 
                        : null,
                    'photo_piece_identite_path' => $livreur->photo_piece_identite_path 
                        ? asset('storage/' . $livreur->photo_piece_identite_path) 
                        : null,
                    'photo_moto_path' => $livreur->photo_moto_path 
                        ? asset('storage/' . $livreur->photo_moto_path) 
                        : null,
    
                    // 📊 Stats imbriquées (optionnel, pour cohérence)
                    'total_livraisons' => $totalLivraisons,
                    'note_moyenne' => $noteMoyenne,
                    'taux_acceptation' => $tauxAcceptation,
                    
                    'phone' => $livreur->phone ?? null,
                    'address' => $livreur->address ?? null,
                    'city' => $livreur->city ?? null,
                    'country' => $livreur->country ?? null,
                ],
            ];
        } 
        // ═══════════════════════════════════════════════════════
        // 🛒 Sinon, on garde la logique VENDEUR
        // ═══════════════════════════════════════════════════════
        else {
            $seller = $user->vendeur ?? null;
            
            $responseData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? ($seller ? $seller->phone : null),
                'address' => $user->address ?? ($seller ? $seller->address : null),
                'city' => $seller ? $seller->city : null,
                'country' => $seller ? $seller->country : null,
                'company_name' => $seller ? $seller->company_name : null,
                'role' => $roleName,
                'avatar' => $seller && $seller->selfie_path 
                    ? asset('storage/' . $seller->selfie_path) 
                    : null,
                'is_verified' => $seller && $seller->status === 'valide' ? 1 : 0,
                'is_seller' => $seller && $seller->status === 'valide' ? 1 : 0,
                'vendeur' => $seller,
                'created_at' => $user->created_at,
            ];
        }
    
        return response()->json([
            'message' => 'Profil récupéré',
            'data' => $responseData
        ]);
    }
    /**
     * Mettre à jour les informations du profil
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $seller = $user->vendeur;
        

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            // Champs spécifiques vendeur (ignorés silencieusement si l'user n'est pas vendeur)
            'company_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Mise à jour de la table `users`
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ]);

        // 2. Mise à jour de la table `sellers` si l'utilisateur est un vendeur
        if ($seller) {
            $seller->update([
                'phone' => $validated['phone'] ?? $seller->phone,
                'address' => $validated['address'] ?? $seller->address,
                'company_name' => $validated['company_name'] ?? $seller->company_name,
                'city' => $validated['city'] ?? $seller->city,
                'country' => $validated['country'] ?? $seller->country,
            ]);
        }

        // On retourne les données mises à jour en réutilisant la méthode show
        return $this->show($request);
    }





        /**
     * Mettre à jour les informations du profil
     */
    public function update2(Request $request)
    {
        $user = $request->user();
        
        

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            // Champs spécifiques vendeur (ignorés silencieusement si l'user n'est pas vendeur)
            'company_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Mise à jour de la table `users`
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ]);

       
        return $this->show2($request);
    }



    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.'
        ]);
    }
}