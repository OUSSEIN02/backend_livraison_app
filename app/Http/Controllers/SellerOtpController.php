<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Seller;

class SellerOtpController extends Controller
{
    /**
     * Générer et envoyer un code OTP par email
     */

     public function sendOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $email = $request->email;

    // Vérifier si l'email existe déjà
    if (Seller::where('email', $email)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Un compte est déjà associé à cette adresse e-mail.'
        ], 409); // 409 = Conflit
    }

    // Vérifier le rate limiting (max 3 envois par 10 minutes)
    $cacheKey = "otp_attempts_{$email}";
    $attempts = Cache::get($cacheKey, 0);

    // if ($attempts >= 3) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Trop de tentatives. Veuillez réessayer dans 10 minutes.'
    //     ], 429);
    // }

    // Générer un code OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Stocker l'OTP
    Cache::put("otp_{$email}", $otp, now()->addMinutes(10));

    // Incrémenter le compteur
    Cache::put($cacheKey, $attempts + 1, now()->addMinutes(10));

    try {
        Mail::to($email)->send(new OtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'Code OTP envoyé avec succès à ' . $email
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}

    /**
     * Vérifier le code OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        // Récupérer l'OTP stocké
        $storedOtp = Cache::get("otp_{$email}");

        if (!$storedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Le code a expiré. Veuillez en demander un nouveau.'
            ], 422);
        }

        // Vérifier le code (comparaison sécurisée)
        if (!hash_equals($storedOtp, $otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP incorrect.'
            ], 422);
        }

        // OTP valide : supprimer du cache et marquer l'email comme vérifié
        Cache::forget("otp_{$email}");
        Cache::forget("otp_attempts_{$email}");
        
        // Marquer l'email comme vérifié (valable 30 minutes pour finaliser l'inscription)
        Cache::put("email_verified_{$email}", true, now()->addMinutes(30));

        return response()->json([
            'success' => true,
            'message' => 'Email vérifié avec succès.'
        ]);
    }

    /**
     * Vérifier si l'email a été vérifié (utilisé avant l'inscription finale)
     */
    public function checkEmailVerified(Request $request)
    {
        $email = $request->email;
        $isVerified = Cache::get("email_verified_{$email}", false);

        return response()->json([
            'verified' => $isVerified
        ]);
    }
}