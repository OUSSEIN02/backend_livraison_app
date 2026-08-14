<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs avec pagination, recherche et filtres.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        // 1. Requête de base avec Eager Loading (CRUCIAL pour éviter les requêtes N+1)
        // On charge l'utilisateur, ses rôles, et les permissions de ces rôles en une seule fois.
        $query = User::with(['roles.permissions']);

        // 2. Filtre de recherche (Nom ou Email)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 3. Filtre par rôle (on utilise le 'slug' envoyé par le frontend)
        if ($roleFilter) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('slug', $roleFilter);
            });
        }

        // 4. Tri et Pagination
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // 5. Transformation des données pour correspondre exactement au format attendu par React
        $transformedUsers = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'avatarlivreur' => $user->livreur?->photo_identite_path ? asset('storage/' . $user->livreur->photo_identite_path) : null,
                'avatarVendeur' => $user->vendeur?->selfie_path ? asset('storage/' . $user->vendeur->selfie_path) : null,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'permissions' => $role->permissions->map(function ($permission) {
                            return [
                                'name' => $permission->name
                            ];
                        })->values() // ->values() réindexe le tableau pour un JSON propre []
                    ];
                })->values()
            ];
        });

        return response()->json([
            'data' => $transformedUsers,
            'total' => $users->total(),
            'last_page' => $users->lastPage(),
            'current_page' => $users->currentPage(),
        ]);
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        // Sécurité : Empêcher l'administrateur de se supprimer lui-même
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 403);
        }

        $userName = $user->name;
        
        // La suppression de l'utilisateur supprimera automatiquement 
        // les entrées dans la table pivot role_user grâce aux contraintes de clé étrangère.
        $user->delete();

        return response()->json([
            'message' => "L'utilisateur '{$userName}' a été supprimé avec succès."
        ], 200);
    }

   

        /**
     * Récupère la liste des rôles avec leurs permissions pour le formulaire frontend.
     */
    public function getRoles()
    {
        // 1. On sélectionne les champs du rôle (l'ID est OBLIGATOIRE pour que la relation belongsToMany fonctionne)
        // 2. On charge la relation 'permissions' en sélectionnant ses champs spécifiques
        $roles = Role::select('id', 'name', 'slug', 'description')
                     ->with('permissions:id,name,slug,description')
                     ->get();

        // 3. On formate la réponse pour éviter d'envoyer des données inutiles (comme les champs pivot de la table de liaison)
        $formattedRoles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                    ];
                })->values(), // ->values() est crucial : il réindexe le tableau JSON en [0, 1, 2...] 
            ];
        });

        return response()->json([
            'data' => $formattedRoles
        ]);
    }





    public function getRoles2()
    {
        // 1. On filtre avec whereNotIn pour exclure 'livreur' et 'vendeur'
        $roles = Role::select('id', 'name', 'slug', 'description')
                    ->whereNotIn('slug', ['livreur', 'vendeur']) // 👈 Exclusion ici
                    ->with('permissions:id,name,slug,description')
                    ->get();

        // 2. On formate la réponse JSON
        $formattedRoles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'data' => $formattedRoles
        ]);
    }
      /**
     * Affiche les détails d'un utilisateur spécifique (pour le mode édition).
     */
    public function show(User $user)
    {
        // Charge l'utilisateur avec ses rôles et les permissions associées
        $user->load(['roles.permissions']);

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                    ])->values()
                ];
            })->values()
        ];

        return response()->json(['data' => $data]);
    }

   
        /**
     * Crée un nouvel utilisateur avec ses rôles.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validation des données reçues du frontend
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ], [
            'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',
            'role_ids.required' => 'Vous devez sélectionner au moins un rôle.',
            'role_ids.min' => 'Vous devez sélectionner au moins un rôle.',
            'role_ids.*.exists' => 'Un ou plusieurs rôles sélectionnés sont invalides.',
        ]);

        // 2. Création de l'utilisateur (le mot de passe est automatiquement haché grâce au cast 'hashed' dans ton modèle, 
        // mais Hash::make est plus explicite et sûr)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Attribution des rôles (table pivot role_user)
        // La méthode sync() attache les IDs des rôles à l'utilisateur
        $user->roles()->sync($validated['role_ids']);

        // 4. Chargement des relations pour la réponse JSON
        $user->load('roles.permissions');

        // 5. Réponse de succès
        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                    ];
                })->values()
            ]
        ], 201); // 201 = Created
    }

    /**
     * Met à jour un utilisateur existant (nom, email, téléphone, rôles).
     * Le mot de passe n'est PAS modifié ici.
     */


         /**
     * Met à jour les informations d'un utilisateur existant.
     * 
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id) // Ignore l'email de l'utilisateur actuel
            ],
            'phone' => 'nullable|string|max:20',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            // Le mot de passe est optionnel ici (seulement si on veut le changer)
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',
            'role_ids.required' => 'Vous devez sélectionner au moins un rôle.',
            'role_ids.min' => 'Vous devez sélectionner au moins un rôle.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Mise à jour des champs de base
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        // Mise à jour du mot de passe SEULEMENT s'il est fourni
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Synchronisation des rôles (supprime les anciens, ajoute les nouveaux)
        $user->roles()->sync($validated['role_ids']);

        // Recharger les rôles avec permissions pour la réponse
        $user->load('roles.permissions');

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'permissions' => $role->permissions->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'slug' => $p->slug,
                        ])->values()
                    ];
                })->values()
            ]
        ]);
    }

    /**
     * Met à jour UNIQUEMENT le mot de passe (via le modal dédié).
     */
    public function updatePassword(Request $request, User $user)
    {
        // Validation stricte : le mot de passe est requis, min 6 caractères, et doit être confirmé
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        // Mise à jour du mot de passe (haché)
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'message' => 'Le mot de passe a été modifié avec succès.'
        ], 200);
    }
}




