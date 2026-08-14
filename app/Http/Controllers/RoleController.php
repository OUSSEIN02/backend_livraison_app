<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    
    public function store(Request $request)
    {
        // 1. Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string|max:500',
            'permission_ids' => 'required|array|min:1',
            'permission_ids.*' => 'exists:permissions,id',
        ], [
            'name.unique' => 'Ce nom de rôle existe déjà.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'permission_ids.required' => 'Vous devez sélectionner au moins une permission.',
            'permission_ids.min' => 'Vous devez sélectionner au moins une permission.',
        ]);

        // 2. Création du rôle
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ]);

        // 3. Attribution des permissions (table pivot permission_role)
        $role->permissions()->sync($validated['permission_ids']);

        // 4. Réponse de succès
        return response()->json([
            'message' => 'Rôle créé avec succès.',
            'data' => $role->load('permissions') // Charge les permissions pour la réponse
        ], 201); // 201 = Created
    }
}