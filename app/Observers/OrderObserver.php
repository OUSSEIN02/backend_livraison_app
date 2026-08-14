<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
    {
        // LOG DE DÉBOGAGE : Vérifiez votre fichier storage/logs/laravel.log après une mise à jour
        Log::info("OrderObserver déclenché. ID: {$order->id}, Nouveau statut: {$order->status}, Modifié: " . ($order->isDirty('status') ? 'Oui' : 'Non'));

        // On vérifie si le statut a changé ET qu'il est maintenant 'payee'
        if ($order->isDirty('status') && $order->status === 'payee') {
            
            $existingInvoice = Invoice::where('order_id', $order->id)->first();
            
            if (!$existingInvoice) {
                // Génération d'un numéro de facture sécurisé (ex: FAC-2026-0001)
                $year = date('Y');
                $count = Invoice::whereYear('created_at', $year)->count() + 1;
                $invoiceNumber = 'FAC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                Invoice::create([
                    'user_id'        => $order->user_id,
                    'order_id'       => $order->id,
                    'invoice_number' => $invoiceNumber,
                    'amount'         => $order->total_amount,
                    'status'         => 'payee',
                    'issued_at'      => now(),
                ]);

                Log::info("Facture générée avec succès pour la commande {$order->id} : {$invoiceNumber}");
            } else {
                Log::warning("Une facture existe déjà pour la commande {$order->id}.");
            }
        }
    }
}