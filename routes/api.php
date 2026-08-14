<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerOtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\LitigeController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\PermissionController;   
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LivreurLocationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DeliveryController;


// =================================================================
// 1. ROUTES PUBLIQUES (Pas d'authentification requise)
// =================================================================
Route::post('/sellers/register', [AuthController::class, 'register']);
Route::post('/livreurs/register', [AuthController::class, 'registerLivreur']);
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('sellers')->group(function () {
    Route::post('/send-otp', [SellerOtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [SellerOtpController::class, 'verifyOtp']);
    Route::post('/check-verified', [SellerOtpController::class, 'checkEmailVerified']);
});

// =================================================================
// 2. ROUTES AUTHENTIFIÉES (Middleware Sanctum)
// =================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- Authentification ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard/pricing', [ZoneController::class, 'getTarification']);
    Route::apiResource('/dashboard/zones', ZoneController::class);
    // --- Profil Utilisateur ---
    Route::get('/user/profile', [ProfileController::class, 'show']);
    Route::get('/livreur/profile', [ProfileController::class, 'show2']);
    Route::put('/livreur/profile', [ProfileController::class, 'update2']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);
    Route::get('/admin/couriers/new-count', [DashboardController::class, 'getNewCouriersCount']);
    Route::get('/admin/sellers/new-count', [DashboardController::class, 'getNewSellersCount']);

    // --- Espace Vendeur (Dashboard) ---
    Route::get('/dashboard/users', [UserController::class, 'index']);
    Route::post('/dashboard/users', [UserController::class, 'store']);
    Route::get('/dashboard/roles', [UserController::class, 'getRoles']);
    Route::post('/dashboard/roles', [RoleController::class, 'store']); 
    Route::get('/dashboard/roles/users', [UserController::class, 'getRoles2']);
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
    Route::get('/orders/history', [OrderController::class, 'history']);
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/mark-as-paid', [OrderController::class, 'markAsPaid']);

    // --- Gestion des Livreurs (Dashboard) ---
    // ⚠️ IMPORTANT : La route spécifique SANS paramètre doit être en PREMIER
    Route::get('/dashboard/statistiques', [StatistiquesController::class, 'index']);
    Route::get('/dashboard/livreurs/disponibles', [AttributionController::class, 'disponibles']);
    Route::get('/dashboard/litiges', [LitigeController::class, 'index']);
    Route::get('/dashboard/users/{user}', [UserController::class, 'show']);
    Route::put('/dashboard/users/{user}', [UserController::class, 'update']);

    // Ensuite les routes avec paramètre dynamique {id}
    Route::get('/dashboard/livreurs', [DashboardController::class, 'index']);
    Route::get('/dashboard/livreurs/{id}', [DashboardController::class, 'showLivreurDetails']);
    Route::put('/dashboard/users/{user}/password', [UserController::class, 'updatePassword']);


    // --- Gestion des Vendeurs (Admin) ---
    // ⚠️ IMPORTANT : Route spécifique avant la route dynamique
    Route::get('/dashboard/vendeurs', [VendeurController::class, 'index']);
    Route::get('/dashboard/vendeurs/{id}', [VendeurController::class, 'show']);
    Route::post('/dashboard/livreurs/{id}/validate', [VendeurController::class, 'validateLivreur']);
    Route::post('/dashboard/livreurs/{id}/suspend', [VendeurController::class, 'suspendLivreur']);
    Route::post('/dashboard/vendeurs/{id}/validate', [VendeurController::class, 'validateVendeur']);
    Route::post('/dashboard/vendeurs/{id}/suspend', [VendeurController::class, 'suspendVendeur']);

    // --- Gestion des Attributions (Admin) ---
    Route::get('/dashboard/orders/pending-attribution', [AttributionController::class, 'pendingAttribution']);
    Route::post('/dashboard/orders/{order}/assign', [AttributionController::class, 'assignLivreur']);

    Route::get('/dashboard/permissions', [PermissionController::class, 'index']);

    

    Route::post('livreur/location', [LivreurLocationController::class, 'updateLocation']);
    Route::get('livreur/nearby', [LivreurLocationController::class, 'getNearbyLivreurs']);


// Route admin pour le nettoyage
   Route::get('/livreur/cleanup', [LivreurLocationController::class, 'cleanupOldLocations']);

  

    Route::get('/delivery-requests', [CourseController::class, 'index']);

    Route::post(
        '/delivery-requests/{requestId}/accept',
        [CourseController::class, 'accept']
    );

    Route::post('/orders/{orderId}/pickup-confirmed', [OrderController::class, 'confirmPickup']);
    Route::post('/orders/{orderId}/delivered', [OrderController::class, 'confirmDelivery']);

    

    Route::post('/deliveries/{orderId}/location', [DeliveryController::class, 'updateLocation']);
    
    
});







