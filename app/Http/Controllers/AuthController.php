<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;
use App\Models\Livreur;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class AuthController extends Controller
{
   
    
    private ImageCompressionService $imageCompressionService;

    // 2. Injection de dépendance via le constructeur
    public function __construct(ImageCompressionService $imageCompressionService)
    {
        $this->imageCompressionService = $imageCompressionService;
    }

  
    public function register(Request $request)
    {
        // 1. Validation des données
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'phone'        => 'required|string|max:20',
            'country'      => 'required|string|size:2',
            'city'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'password'     => 'required|string|min:6|confirmed', 
            
            'id_front'     => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'id_back'      => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie'       => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'company_name.required' => 'Le nom de l\'entreprise est requis.',
            'email.required'        => 'L\'email est requis.',
            'email.email'           => 'L\'email doit être une adresse email valide.',
            'email.unique'          => 'Cet email est déjà utilisé.',
            'phone.required'        => 'Le numéro de téléphone est requis.',
            'country.required'      => 'Le pays est requis.',
            'country.size'          => 'Le code pays doit contenir exactement 2 caractères.',
            'city.required'         => 'La ville est requise.',
            'address.required'      => 'L\'adresse est requise.',
            'password.required'     => 'Le mot de passe me correspond pas.',
            'password.min'          => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed'    => 'La confirmation du mot de passe ne correspond pas.',
            'id_front.required'     => 'La photo de la pièce d\'identité (recto) est requise.',
            'id_front.max'          => 'La photo du recto ne doit pas dépasser 5 Mo.',
            'id_back.required'      => 'La photo du verso est requise.',
            'id_back.max'           => 'La photo du verso ne doit pas dépasser 5 Mo.',
            'selfie.required'       => 'La photo selfie est requise.',
            'selfie.max'            => 'La photo selfie ne doit pas dépasser 5 Mo.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {

                // 2. Traitement des images
                $this->imageCompressionService
                    ->setQuality(80)
                    ->setMaxDimensions(1920, 1080);

                $pathIdFront = $this->imageCompressionService->compressAndStore(
                    $request->file('id_front'),
                    'sellers/kyc'
                );

                $pathIdBack = $this->imageCompressionService->compressAndStore(
                    $request->file('id_back'),
                    'sellers/kyc'
                );

                $pathSelfie = $this->imageCompressionService->compressAndStore(
                    $request->file('selfie'),
                    'sellers/kyc'
                );

                // 3. Création du compte utilisateur (Auth)
                $user = User::create([
                    'name'     => $request->company_name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                // 4. Assignation du rôle "vendeur"
                $roleVendeur = Role::where('slug', 'vendeur')
                                ->orWhere('name', 'vendeur')
                                ->first();

                if ($roleVendeur) {
                    $user->roles()->attach($roleVendeur->id);
                } else {
                    throw new \Exception("Le rôle 'vendeur' n'existe pas en base de données.");
                }

                // 5. Création du profil vendeur
                $seller = Seller::create([
                    'user_id'       => $user->id,
                    'company_name'  => $request->company_name,
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'country'       => $request->country,
                    'city'          => $request->city,
                    'address'       => $request->address,
                    'id_front_path' => $pathIdFront,
                    'id_back_path'  => $pathIdBack,
                    'selfie_path'   => $pathSelfie,
                    'status'        => 'en_attente',
                ]);

                // 6. Génération du Token d'authentification (Sanctum)
                $token = $user->createToken('auth_token')->plainTextToken;

                // 7. Chargement des permissions
                $user->load('roles.permissions');
                $permissions = $user->roles->pluck('permissions')->flatten()->pluck('slug')->unique()->values();

                // 8. Réponse de succès avec Token
                return response()->json([
                    'message'      => 'Inscription réussie. Votre compte est en cours de vérification.',
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'data'         => [
                        'user'        => $user,
                        'seller'      => $seller,
                        'permissions' => $permissions,
                    ]
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // public function registerLivreur(Request $request)
    // {
    //     // 2. Validation des données
    //     $validator = Validator::make($request->all(), [
    //         'nom'                => 'required|string|max:255',
    //         'prenom'             => 'required|string|max:255',
    //         'whatsapp'           => 'required|string|max:20',
    //         'email'              => 'required|string|email|max:255|unique:users,email',
    //         'password'           => 'required|string|min:6|confirmed',
            
    //         'photo_identite'     => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5 Mo
    //         'photo_piece_identite' => 'required|image|mimes:jpeg,png,jpg|max:5120',
    //         'photo_moto'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
            
    //         'numero_plaque'      => 'required|string|max:20',
    //         'etat_moto'          => 'required|string|in:excellent,bon,moyen,correct',
    //         'experience'         => 'required|in:oui,non',
    //         'zones_livraison'    => 'required|array|min:1',
    //         'zones_livraison.*'  => 'string',
    //     ], [
    //         'nom.required'                   => 'Le nom est requis.',
    //         'prenom.required'                => 'Le prénom est requis.',
    //         'whatsapp.required'              => 'Le numéro WhatsApp est requis.',
    //         'email.required'                 => 'L\'email est requis.',
    //         'email.email'                    => 'L\'email doit être une adresse email valide.',
    //         'email.unique'                   => 'Cet email est déjà utilisé.',
    //         'password.required'              => 'Le mot de passe est requis.',
    //         'password.min'                   => 'Le mot de passe doit contenir au moins 6 caractères.',
    //         'password.confirmed'             => 'La confirmation du mot de passe ne correspond pas.',
    //         'photo_identite.required'        => 'La photo d\'identité (selfie) est requise.',
    //         'photo_piece_identite.required'  => 'La photo de la pièce d\'identité est requise.',
    //         'photo_moto.required'            => 'La photo de la moto est requise.',
    //         'numero_plaque.required'         => 'Le numéro de plaque est requis.',
    //         'etat_moto.required'             => 'L\'état de la moto est requis.',
    //         'experience.required'            => 'Veuillez indiquer votre expérience.',
    //         'zones_livraison.required'       => 'Veuillez sélectionner au moins une zone de livraison.',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Erreur de validation',
    //             'errors'  => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         // 3. Transaction de base de données pour garantir l'intégrité des données
    //         return DB::transaction(function () use ($request) {

    //             // 4. Traitement et compression des images
    //             $this->imageCompressionService
    //                 ->setQuality(80)
    //                 ->setMaxDimensions(1920, 1080);

    //             $pathPhotoIdentite = $this->imageCompressionService->compressAndStore(
    //                 $request->file('photo_identite'),
    //                 'livreurs/identite'
    //             );

    //             $pathPhotoPiece = $this->imageCompressionService->compressAndStore(
    //                 $request->file('photo_piece_identite'),
    //                 'livreurs/pieces'
    //             );

    //             $pathPhotoMoto = $this->imageCompressionService->compressAndStore(
    //                 $request->file('photo_moto'),
    //                 'livreurs/motos'
    //             );

    //             // 5. Création du compte utilisateur (Auth)
    //             $user = User::create([
    //                 'name'     => trim($request->nom . ' ' . $request->prenom),
    //                 'email'    => $request->email,
    //                 'phone'    => $request->whatsapp, // On stocke le WhatsApp dans le champ phone de User
    //                 'password' => Hash::make($request->password),
    //             ]);

    //             // 6. Assignation du rôle "livreur"
    //             $roleLivreur = Role::where('slug', 'livreur')
    //                             ->orWhere('name', 'livreur')
    //                             ->first();

    //             if ($roleLivreur) {
    //                 $user->roles()->attach($roleLivreur->id);
    //             } else {
    //                 // Fallback : si le rôle n'existe pas, on crée une exception pour annuler la transaction
    //                 throw new \Exception("Le rôle 'livreur' n'existe pas en base de données.");
    //             }

    //             // 7. Création du profil Livreur
    //             // ⚠️ IMPORTANT : Assurez-vous que ces noms de colonnes correspondent à votre table 'livreurs' 
    //             // et qu'ils sont présents dans le tableau $fillable du modèle Livreur.
    //             $livreur = Livreur::create([
    //                 'user_id'                  => $user->id,
    //                 'numero_plaque'            => strtoupper($request->numero_plaque),
    //                 'etat_moto'                => $request->etat_moto,
    //                 'experience'               => $request->experience,
    //                 'zones_livraison'          => json_encode($request->zones_livraison), // Ou utilisez un cast 'array'/'json' dans le modèle
    //                 'photo_identite_path'      => $pathPhotoIdentite,
    //                 'photo_piece_identite_path'=> $pathPhotoPiece,
    //                 'photo_moto_path'          => $pathPhotoMoto,
    //                 'status'                   => 'en_attente', // Statut par défaut en attendant validation admin
    //             ]);

    //             // 8. Génération du Token d'authentification (Sanctum) - Optionnel
    //             // Si vous voulez qu'ils puissent se connecter immédiatement, gardez ceci.
    //             // Sinon, supprimez ces lignes s'ils doivent attendre la validation manuelle d'un admin.
    //             $token = $user->createToken('auth_token')->plainTextToken;

    //             // 9. Réponse de succès
    //             return response()->json([
    //                 'message'      => 'Inscription réussie ! Votre dossier est en cours de vérification par nos équipes.',
    //                 'access_token' => $token,
    //                 'token_type'   => 'Bearer',
    //                 'data'         => [
    //                     'user'   => $user,
    //                     'livreur'=> $livreur,
    //                 ]
    //             ], 201);
    //         });

    //     } catch (\Exception $e) {
    //         // En cas d'erreur, la transaction DB::transaction annulera automatiquement 
    //         // la création de l'utilisateur et du livreur, évitant les données orphelines.
    //         return response()->json([
    //             'message' => 'Une erreur est survenue lors de l\'inscription.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }  



    public function registerLivreur(Request $request)
    {
        // 1. Validation des données
        $validator = Validator::make($request->all(), [
            'nom'                   => 'required|string|max:255',
            'prenom'                => 'required|string|max:255',
            'whatsapp'              => 'required|string|max:20',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            
            'photo_identite'        => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'photo_piece_identite'  => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'photo_moto'            => 'required|image|mimes:jpeg,png,jpg|max:5120',
            
            'numero_plaque'         => 'required|string|max:20',
            'etat_moto'             => 'required|string|in:excellent,bon,moyen,correct',
            'experience'            => 'required|in:oui,non',
            'zones_livraison'       => 'required|array|min:1',
            'zones_livraison.*'     => 'string',
            
            // Champ optionnel pour l'admin (défaut: en_attente)
            'status'                => 'nullable|string|in:en_attente,actif,suspendu',
        ], [
            // Messages personnalisés en français
            'nom.required'                      => 'Le nom est requis.',
            'nom.max'                           => 'Le nom ne doit pas dépasser 255 caractères.',
            
            'prenom.required'                   => 'Le prénom est requis.',
            'prenom.max'                        => 'Le prénom ne doit pas dépasser 255 caractères.',
            
            'whatsapp.required'                 => 'Le numéro WhatsApp est requis.',
            'whatsapp.max'                      => 'Le numéro WhatsApp ne doit pas dépasser 20 caractères.',
            
            'email.required'                    => 'L\'email est requis.',
            'email.email'                       => 'L\'email doit être une adresse email valide.',
            'email.max'                         => 'L\'email ne doit pas dépasser 255 caractères.',
            'email.unique'                      => 'Cet email est déjà utilisé.',
            
            'password.required'                 => 'Le mot de passe est requis.',
            'password.min'                      => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed'                => 'La confirmation du mot de passe ne correspond pas.',
            
            'photo_identite.required'           => 'La photo d\'identité (selfie) est requise.',
            'photo_identite.image'              => 'La photo d\'identité doit être une image.',
            'photo_identite.mimes'              => 'La photo d\'identité doit être au format JPEG, PNG ou JPG.',
            'photo_identite.max'                => 'La photo d\'identité ne doit pas dépasser 5 Mo.',
            
            'photo_piece_identite.required'     => 'La photo de la pièce d\'identité est requise.',
            'photo_piece_identite.image'        => 'La photo de la pièce d\'identité doit être une image.',
            'photo_piece_identite.mimes'        => 'La photo de la pièce d\'identité doit être au format JPEG, PNG ou JPG.',
            'photo_piece_identite.max'          => 'La photo de la pièce d\'identité ne doit pas dépasser 5 Mo.',
            
            'photo_moto.required'               => 'La photo du véhicule est requise.',
            'photo_moto.image'                  => 'La photo du véhicule doit être une image.',
            'photo_moto.mimes'                  => 'La photo du véhicule doit être au format JPEG, PNG ou JPG.',
            'photo_moto.max'                    => 'La photo du véhicule ne doit pas dépasser 5 Mo.',
            
            'numero_plaque.required'            => 'Le numéro de plaque est requis.',
            'numero_plaque.max'                 => 'Le numéro de plaque ne doit pas dépasser 20 caractères.',
            
            'etat_moto.required'                => 'L\'état du véhicule est requis.',
            'etat_moto.in'                      => 'L\'état du véhicule doit être: excellent, bon, moyen ou correct.',
            
            'experience.required'               => 'Veuillez indiquer votre expérience.',
            'experience.in'                     => 'L\'expérience doit être "oui" ou "non".',
            
            'zones_livraison.required'          => 'Veuillez sélectionner au moins une zone de livraison.',
            'zones_livraison.array'             => 'Les zones de livraison doivent être un tableau.',
            'zones_livraison.min'               => 'Veuillez sélectionner au moins une zone de livraison.',
            'zones_livraison.*.string'          => 'Chaque zone de livraison doit être une chaîne de caractères.',
            
            'status.in'                         => 'Le statut doit être: en_attente, actif ou suspendu.',
        ]);

        // 2. Retour des erreurs de validation
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 3. Transaction de base de données pour garantir l'intégrité des données
            return DB::transaction(function () use ($request) {

                // 4. Configuration et traitement des images
                $this->imageCompressionService
                    ->setQuality(80)
                    ->setMaxDimensions(1920, 1080);

                // Compression et stockage des images
                $pathPhotoIdentite = $this->imageCompressionService->compressAndStore(
                    $request->file('photo_identite'),
                    'livreurs/identite'
                );

                $pathPhotoPiece = $this->imageCompressionService->compressAndStore(
                    $request->file('photo_piece_identite'),
                    'livreurs/pieces'
                );

                $pathPhotoMoto = $this->imageCompressionService->compressAndStore(
                    $request->file('photo_moto'),
                    'livreurs/motos'
                );

                // 5. Création du compte utilisateur (Auth)
                $user = User::create([
                    'name'     => trim($request->nom . ' ' . $request->prenom),
                    'email'    => $request->email,
                    'phone'    => $request->whatsapp,
                    'password' => Hash::make($request->password),
                ]);

                // 6. Assignation du rôle "livreur"
                $roleLivreur = Role::where('slug', 'livreur')
                                ->orWhere('name', 'livreur')
                                ->first();

                if ($roleLivreur) {
                    $user->roles()->attach($roleLivreur->id);
                } else {
                    throw new \Exception("Le rôle 'livreur' n'existe pas en base de données.");
                }

                // 7. Création du profil Livreur
                $livreur = Livreur::create([
                    'user_id'                   => $user->id,
                    'numero_plaque'             => strtoupper($request->numero_plaque),
                    'etat_moto'                 => $request->etat_moto,
                    'experience'                => $request->experience,
                    'zones_livraison'           => json_encode($request->zones_livraison),
                    'photo_identite_path'       => $pathPhotoIdentite,
                    'photo_piece_identite_path' => $pathPhotoPiece,
                    'photo_moto_path'           => $pathPhotoMoto,
                    'status'                    => $request->input('status', 'en_attente'),
                ]);

                // 8. Génération du Token d'authentification (Sanctum)
                $token = $user->createToken('auth_token')->plainTextToken;

                // 9. Réponse de succès
                return response()->json([
                    'message'      => 'Inscription réussie ! Votre dossier est en cours de vérification par nos équipes.',
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'data'         => [
                        'user'    => $user,
                        'livreur' => $livreur,
                    ]
                ], 201);
            });

        } catch (\Exception $e) {
            // En cas d'erreur, la transaction annule automatiquement les créations
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Vérification des identifiants
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Génération du token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Chargement des relations rôles, permissions et profil vendeur
        $user->load(['roles.permissions']);

        // Extraction sous forme de tableau plat des slugs de permissions
        $permissions = $user->roles
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->unique()
            ->values();

        return response()->json([
            'message'      => 'Connexion réussie',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => [
                'user'        => $user,
                'seller'      => $user->seller,
                'permissions' => $permissions,
                'avatar'      => $user->vendeur ? asset('storage/' . $user->vendeur->selfie_path) : null,
                'avatar2'      => $user->livreur ? asset('storage/' . $user->livreur->photo_identite_path) : null,
            ]
        ], 200);
    }

//     public function registerVendeur(Request $request) 
// {
//     return DB::transaction(function () use ($request) {
//         // 1. Créer l'utilisateur dans la table 'users'
//         $user = User::create([
//             'name' => $request->company_name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//         ]);

//         // 2. Récupérer le rôle "vendeur" depuis la table 'roles'
//         $roleVendeur = Role::where('slug', 'vendeur')->first(); 
//         // Note : Utilisez 'name' ou 'slug' selon le nom de votre colonne dans la table roles

//         if ($roleVendeur) {
//             // Insère une ligne dans la table pivot 'role_user' (user_id, role_id)
//             $user->roles()->attach($roleVendeur->id);
//         }

//         // 3. Créer le profil vendeur spécifique lié à cet utilisateur
//         Vendeur::create([
//             'user_id' => $user->id, // Liaison ForeignKey avec la table users
//             'siret'   => $request->siret,
//             'phone'   => $request->phone,
//             'status'  => 'en_attente',
//         ]);

//         return response()->json(['message' => 'Inscription du vendeur réussie'], 201);
//     });
// }


    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'firstname' => 'required',
    //         'lastname' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:6',
    //     ]);

    //     $user = User::create([
    //         'firstname' => $request->firstname,
    //         'lastname' => $request->lastname,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token
    //     ]);
    // }


    public function logout(Request $request)
    {
        // Supprime uniquement le token utilisé pour la requête actuelle
        $request->user()->currentAccessToken()->delete();

        // Optionnel : si tu veux déconnecter l'utilisateur de TOUS ses appareils
        // $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }
}






    






