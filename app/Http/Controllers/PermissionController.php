<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Récupère toutes les permissions, groupées par catégorie pour le frontend.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // 1. Récupérer toutes les permissions avec les champs nécessaires
        $permissions = Permission::select('id', 'name', 'slug', 'description')->get();

        // 2. Grouper les permissions par préfixe (ex: "users.view" -> groupe "Users")
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            // On sépare "users.view" par le point "." et on prend la première partie
            $parts = explode('.', $permission->name);
            // On met la première lettre en majuscule pour un joli nom de groupe (ex: "Users")
            return ucfirst($parts[0]); 
        })->map(function ($group, $groupName) {
            return [
                'name' => $groupName,
                'permissions' => $group->map(function ($perm) {
                    return [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'slug' => $perm->slug,
                        'description' => $perm->description ?? 'Aucune description disponible',
                    ];
                })->values() // ->values() réindexe le tableau JSON proprement
            ];
        })->values(); // ->values() final pour avoir un tableau JSON [ {...}, {...} ]

        return response()->json([
            'data' => $groupedPermissions
        ]);
    }
}