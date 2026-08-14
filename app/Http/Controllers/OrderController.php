<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ImageCompressionService; // <-- N'oubliez pas d'importer votre service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;

class OrderController extends Controller
{
    /**
     * Service de compression d'images
     *
     * @var ImageCompressionService
     */
    protected $imageCompressionService;

    /**
     * Injection de dépendance du service
     */
    public function __construct(ImageCompressionService $imageCompressionService)
    {
        $this->imageCompressionService = $imageCompressionService;
    }

    /**
     * Créer une nouvelle commande de livraison
     */
    public function store(Request $request)
    {
        // 1. Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // 2. Validation des données
        $validator = Validator::make($request->all(), [
            // Adresses de récupération
            'pickup_name' => 'required|string|max:255',
            'pickup_address' => 'required|string|max:255',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',

            // Adresses de livraison
            'dropoff_name' => 'required|string|max:255',
            'dropoff_address' => 'required|string|max:255',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',

            // Détails du colis
            'weight' => 'nullable|string|max:50',
            'is_fragile' => 'boolean',
            'declared_value' => 'nullable|numeric|min:0',
            
            // Type de livraison
            'delivery_type' => 'required|in:immediat,programme',
            'scheduled_date' => 'required_if:delivery_type,programme|nullable|date|after:now',
            'instructions' => 'nullable|string|max:1000',

            // Photo du colis
            'package_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'pickup_name.required' => 'Le nom du lieu de récupération est requis.',
            'pickup_address.required' => 'L\'adresse de récupération est requise.',
            'pickup_lat.required' => 'Veuillez sélectionner une adresse de récupération valide sur la carte.',
            'pickup_lng.required' => 'Veuillez sélectionner une adresse de récupération valide sur la carte.',
            
            'dropoff_name.required' => 'Le nom du destinataire est requis.',
            'dropoff_address.required' => 'L\'adresse de livraison est requise.',
            'dropoff_lat.required' => 'Veuillez sélectionner une adresse de livraison valide sur la carte.',
            'dropoff_lng.required' => 'Veuillez sélectionner une adresse de livraison valide sur la carte.',
            
            'delivery_type.required' => 'Le type de livraison est requis.',
            'delivery_type.in' => 'Le type de livraison doit être "immediat" ou "programme".',
            'scheduled_date.required_if' => 'La date et l\'heure sont requises pour une livraison programmée.',
            'scheduled_date.after' => 'La date de livraison programmée doit être dans le futur.',
            
            'package_photo.required' => 'La photo du colis est obligatoire.',
            'package_photo.image' => 'Le fichier doit être une image.',
            'package_photo.mimes' => 'L\'image doit être au format JPEG, PNG ou JPG.',
            'package_photo.max' => 'La photo du colis ne doit pas dépasser 5 Mo.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Traitement de la transaction
        try {
            DB::beginTransaction();

            // a. Compression et stockage de la photo du colis
            $photoPath = null;
            if ($request->hasFile('package_photo')) {
                // Configuration optionnelle (qualité et dimensions max)
                $this->imageCompressionService
                    ->setQuality(80)
                    ->setMaxDimensions(1920, 1080);

                // Compression et enregistrement dans le dossier "orders/package_photos"
                $photoPath = $this->imageCompressionService->compressAndStore(
                    $request->file('package_photo'),
                    'orders/package_photos'
                );
            }

            // b. Création de la commande en base de données
            $order = Order::create([
                'user_id' => $user->id,
                
                'pickup_name' => $request->pickup_name,
                'pickup_address' => $request->pickup_address,
                'pickup_lat' => $request->pickup_lat,
                'pickup_lng' => $request->pickup_lng,
                
                'dropoff_name' => $request->dropoff_name,
                'dropoff_address' => $request->dropoff_address,
                'dropoff_lat' => $request->dropoff_lat,
                'dropoff_lng' => $request->dropoff_lng,
                
                'weight' => $request->weight,
                'is_fragile' => $request->boolean('is_fragile'),
                'declared_value' => $request->declared_value ?? 0,
                
                'delivery_type' => $request->delivery_type,
                'scheduled_date' => $request->scheduled_date,
                'instructions' => $request->instructions,
                
                'package_photo' => $photoPath,
                'status' => 'en_attente', 
            ]);

            DB::commit();

            // 4. Réponse de succès
            return response()->json([
                'message' => 'Commande créée avec succès. En attente d\'attribution à un livreur.',
                'data' => [
                    'order_id' => $order->id,
                    'reference' => 'CMD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'status' => $order->status,
                    'pickup_address' => $order->pickup_address,
                    'dropoff_address' => $order->dropoff_address,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Suppression de l'image si la transaction a échoué après la compression
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la commande.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur.'
            ], 500);
        }
    }

 
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
    
        // 1. Déterminer si l'utilisateur est restreint (Vendeur ou Livreur)
        $isRestrictedRole = $user->roles()
                                 ->whereIn('slug', ['vendeur', 'livreur'])
                                 ->exists();
    
        // 2. Préparer la requête de base avec les relations
        $query = Order::with(['livreur.user', 'vendeur']);
    
        // 3. Appliquer le filtre utilisateur uniquement SI c'est un rôle restreint (vendeur/livreur)
        if ($isRestrictedRole) {
            $query->where('user_id', $user->id);
        }
    
        // 4. Filtre par statut si envoyé dans la requête (ex: /orders?status=en_attente)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
    
        // 5. Tri et Pagination
        $perPage = $request->get('per_page', 10);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);
    
        // 6. Réponse JSON structurée
        return response()->json([
            'message' => 'Commandes récupérées avec succès.',
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
            ]
        ], 200);
    }


    // /**
    //  * Afficher les détails d'une commande spécifique
    //  */
  
    //  public function show($id)
    //  {
    //      $user = Auth::user();
 
    //      // On récupère la commande avec les relations, en vérifiant qu'elle appartient au vendeur connecté
    //      $order = Order::with(['livreur.user', 'vendeur'])
    //          ->where('id', $id)
    //          ->where('user_id', $user->vendeur->id)
    //          ->first();
 
    //      if (!$order) {
    //          return response()->json(['message' => 'Commande introuvable ou accès non autorisé.'], 404);
    //      }
 
    //      // Formatage des données pour le frontend
    //      $formattedOrder = [
    //          'id' => $order->id,
    //          'reference' => 'CMD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
    //          'status' => $order->status,
             
    //          // Infos Expéditeur (Vendeur)
    //          'pickup_name' => $order->pickup_name,
    //          'pickup_address' => $order->pickup_address,
    //          'pickup_lat' => $order->pickup_lat,
    //          'pickup_lng' => $order->pickup_lng,
    //          'vendeur_company' => $order->vendeur->company_name ?? 'Mon Entreprise',
             
    //          // Infos Destinataire (Client)
    //          'dropoff_name' => $order->dropoff_name,
    //          'dropoff_address' => $order->dropoff_address,
    //          'dropoff_lat' => $order->dropoff_lat,
    //          'dropoff_lng' => $order->dropoff_lng,
             
    //          // Détails colis
    //          'weight' => $order->weight,
    //          'is_fragile' => $order->is_fragile,
    //          'declared_value' => $order->declared_value,
    //          'delivery_type' => $order->delivery_type,
    //          'scheduled_date' => $order->scheduled_date,
    //          'instructions' => $order->instructions,
             
    //          // Photo (Conversion du chemin stocké en URL accessible)
    //          'package_photo_url' => $order->package_photo ? Storage::url($order->package_photo) : null,
             
    //          // Livreur
    //          'livreur_name' => $order->livreur ? ($order->livreur->user->name ?? 'Livreur') : 'Non assigné',
    //          'livreur_phone' => $order->livreur ? ($order->livreur->user->phone ?? null) : null,
             
    //          // Financier & Dates
    //          'total_amount' => $order->total_amount,
    //          'created_at' => $order->created_at,
    //          'delivered_at' => $order->delivered_at,
    //      ];
 
    //      return response()->json([
    //          'message' => 'Détails de la commande récupérés.',
    //          'data' => $formattedOrder
    //      ], 200);
    //  }



    /**
 * Afficher les détails d'une commande spécifique
 */

 public function show($id)
 {
     /** @var \App\Models\User $user */
     $user = Auth::user();
 
     // 1. Déterminer si l'utilisateur est restreint (Vendeur ou Livreur)
     $isRestrictedRole = $user->roles()
                              ->whereIn('slug', ['vendeur', 'livreur'])
                              ->exists();
 
     // 2. Préparer la requête pour trouver la commande par son ID
     $query = Order::with(['livreur.user', 'vendeur'])->where('id', $id);
 
     // 3. Appliquer le filtre d'appartenance uniquement SI c'est un rôle restreint
     if ($isRestrictedRole) {
         $query->where('user_id', $user->id);
     }
 
     $order = $query->first();
 
     // 4. Si la commande n'existe pas ou si l'accès n'est pas autorisé
     if (!$order) {
         return response()->json([
             'message' => 'Commande introuvable ou accès non autorisé.'
         ], 404);
     }
 
     // 5. Formatage des données pour le frontend
     $formattedOrder = [
         'id' => $order->id,
         'reference' => 'CMD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
         'status' => $order->status,
         
         // Infos Expéditeur (Vendeur)
         'pickup_name' => $order->pickup_name,
         'pickup_address' => $order->pickup_address,
         'pickup_lat' => $order->pickup_lat,
         'pickup_lng' => $order->pickup_lng,
         'vendeur_company' => $order->vendeur->company_name ?? $order->user->name ?? 'Mon Entreprise',
         
         // Infos Destinataire (Client)
         'dropoff_name' => $order->dropoff_name,
         'dropoff_address' => $order->dropoff_address,
         'dropoff_lat' => $order->dropoff_lat,
         'dropoff_lng' => $order->dropoff_lng,
         
         // Détails colis
         'weight' => $order->weight,
         'is_fragile' => $order->is_fragile,
         'declared_value' => $order->declared_value,
         'delivery_type' => $order->delivery_type,
         'scheduled_date' => $order->scheduled_date,
         'instructions' => $order->instructions,
         
         // Photo (Conversion du chemin stocké en URL accessible)
         'package_photo_url' => $order->package_photo ? Storage::url($order->package_photo) : null,
         
         // Livreur
         'livreur_name' => $order->livreur ? ($order->livreur->user->name ?? 'Livreur') : 'Non assigné',
         'livreur_phone' => $order->livreur ? ($order->livreur->user->phone ?? null) : null,
         
         // Financier & Dates
         'total_amount' => $order->total_amount,
         'created_at' => $order->created_at,
         'delivered_at' => $order->delivered_at,
     ];
 
     return response()->json([
         'message' => 'Détails de la commande récupérés.',
         'data' => $formattedOrder
     ], 200);
 }

     // Dans votre OrderController.php

    public function history(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 50); // 10 éléments par page par défaut

        // 1. Filtrer uniquement les commandes de l'historique (ajustez les statuts selon votre BDD)
        // 2. Utiliser paginate() au lieu de get()
        $orders = Order::with('livreur') // Charge la relation livreur
            ->where('user_id', $user->id)
            ->whereIn('status', ['livree', 'annulee','en_attente', 'terminee','payee']) 
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Laravel retourne nativement un objet de pagination. 
        // On le formate pour qu'il corresponde à la structure attendue par votre frontend
        return response()->json([
            'message' => 'Historique récupéré avec succès.',
            'data' => $orders->items(), // Les données brutes de la page actuelle
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
                'from'         => $orders->firstItem(),
                'to'           => $orders->lastItem(),
            ]
        ], 200);
    }


    
    public function markAsPaid(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // 1. Changer le statut
        $order->status = 'payee';
        $order->save();

        // 2. Vérifier et créer la facture + le PDF
        $existingInvoice = Invoice::where('order_id', $order->id)->first();
        
        if (!$existingInvoice) {
            $year = date('Y');
            $count = Invoice::whereYear('created_at', $year)->count() + 1;
            $invoiceNumber = 'FAC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // --- GÉNÉRATION DU PDF ---
            $pdf = Pdf::loadView('invoices.pdf', [
                'order' => $order,
                'invoiceNumber' => $invoiceNumber,
                'issuedAt' => now()
            ]);

            // Nom du fichier (ex: FAC-2026-0001.pdf)
            $fileName = $invoiceNumber . '.pdf';
            $filePath = 'invoices/' . $fileName;

            // Sauvegarder le PDF dans le dossier storage/app/public/invoices/
            Storage::disk('public')->put($filePath, $pdf->output());

            // 3. Enregistrer en base de données AVEC le chemin du PDF
            Invoice::create([
                'user_id'        => $order->user_id,
                'order_id'       => $order->id,
                'invoice_number' => $invoiceNumber,
                'amount'         => $order->total_amount,
                'status'         => 'payee',
                'issued_at'      => now(),
                'pdf_path'       => $filePath, // <-- C'est ici qu'on sauve le lien
            ]);
        }

        return response()->json([
            'message' => 'Commande marquée comme payée et facture PDF générée avec succès.'
        ]);
    }

    public function confirmPickup($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'en_cours_livraison', 'picked_up_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'Colis récupéré']);
    }

    public function confirmDelivery($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'livree', 'delivered_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'Livraison terminée']);
    }
}






