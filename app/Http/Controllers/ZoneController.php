<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    /**
     * Récupérer toutes les zones (GET /api/admin/zones)
     */
    public function index(Request $request)
    {
        $query = Zone::query();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Filtre par statut
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('statut', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $zones = $query->orderBy('nom')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $zones->items(),
            'total' => $zones->total(),
            'per_page' => $zones->perPage(),
            'current_page' => $zones->currentPage(),
            'last_page' => $zones->lastPage()
        ]);
    }

    /**
     * Enregistrer les zones (POST /api/admin/zones)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zones' => 'required|array|min:1',
            'zones.*' => 'required|string',
            'tarif_km' => 'required|numeric|min:0',
            'statut' => 'nullable|in:actif,inactif'
        ], [
            'zones.required' => 'Veuillez sélectionner au moins une zone.',
            'zones.min' => 'Veuillez sélectionner au moins une zone.',
            'tarif_km.required' => 'Le tarif au kilomètre est requis.',
            'tarif_km.min' => 'Le tarif doit être supérieur ou égal à 0.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $zonesSelectionnees = $request->zones;
                $tarifKm = $request->tarif_km;
                $statut = $request->statut ?? 'actif';

                // Codes abrégés pour chaque province
                $codesZones = [
                    'Estuaire (Libreville, Owendo, Akanda)' => 'EST',
                    'Haut-Ogooué (Franceville)' => 'HAU',
                    'Moyen-Ogooué (Lambaréné)' => 'MOY',
                    'Ngounié (Mouila)' => 'NGN',
                    'Nyanga (Tchibanga)' => 'NYA',
                    'Ogooué-Ivindo (Makokou)' => 'OGO-IV',
                    'Ogooué-Lolo (Koulamoutou)' => 'OGO-LO',
                    'Ogooué-Maritime (Port-Gentil)' => 'OGO-MA',
                    'Woleu-Ntem (Oyem)' => 'WOL'
                ];

                foreach ($zonesSelectionnees as $zone) {
                    Zone::updateOrCreate(
                        ['nom' => $zone],
                        [
                            'code' => $codesZones[$zone] ?? strtoupper(substr($zone, 0, 5)),
                            'tarif_km' => $tarifKm,
                            'statut' => $statut
                        ]
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($request->zones) > 1
                    ? count($request->zones) . ' zones enregistrées avec succès.'
                    : 'Zone enregistrée avec succès.',
                'count' => count($request->zones)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement des zones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une zone (GET /api/admin/zones/{id})
     */
    public function show($id)
    {
        $zone = Zone::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $zone
        ]);
    }

    /**
     * Mettre à jour une zone (PUT /api/admin/zones/{id})
     */
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tarif_km' => 'sometimes|numeric|min:0',
            'statut' => 'sometimes|in:actif,inactif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $zone->update($request->only(['tarif_km', 'statut']));

        return response()->json([
            'success' => true,
            'message' => 'Zone mise à jour avec succès.',
            'data' => $zone
        ]);
    }

    /**
     * Supprimer une zone (DELETE /api/admin/zones/{id})
     */
    public function destroy($id)
    {
        try {
            $zone = Zone::findOrFail($id);
            $zone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Zone supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.'
            ], 500);
        }
    }


    /**
 * Récupérer la tarification applicable pour les clients
 * GET /api/pricing
 */
public function getTarification()
{
    // Récupère toutes les zones actives triées par nom
    $zonesActives = Zone::where('statut', 'actif')
        ->orderBy('nom')
        ->get(['id', 'nom', 'code', 'tarif_km']);

    if ($zonesActives->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Aucune zone active disponible.',
            'price_per_km' => 0,
        ], 404);
    }

    // Priorité : Estuaire (Libreville) sinon la première zone active
    $zonePrincipale = $zonesActives->firstWhere('code', 'EST') 
                   ?? $zonesActives->first();

    return response()->json([
        'success' => true,
        'price_per_km' => (float) $zonePrincipale->tarif_km,
        'zone_active' => [
            'id' => $zonePrincipale->id,
            'nom' => $zonePrincipale->nom,
            'code' => $zonePrincipale->code,
        ],
        // Optionnel : toutes les zones si vous voulez filtrer côté client
        'all_zones' => $zonesActives->map(fn($z) => [
            'code' => $z->code,
            'nom' => $z->nom,
            'tarif_km' => (float) $z->tarif_km,
        ]),
        'min_fee' => 1500,            // Tarif minimum (peut venir d'une config)
        'fragile_surcharge' => 500,   // Supplément fragile
    ]);
}
}