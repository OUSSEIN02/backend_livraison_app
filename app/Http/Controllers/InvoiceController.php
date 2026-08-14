<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);

        // On récupère les factures de l'utilisateur, avec la référence de la commande associée
        $invoices = Invoice::with('order:id')
            ->where('user_id', $user->id)
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);

        // On formate les données pour qu'elles correspondent exactement aux attentes du frontend React
        $formattedData = $invoices->getCollection()->map(function ($invoice) {
            return [
                'id'              => $invoice->id,
                'invoice_number'  => $invoice->invoice_number,
                'order_id'        => $invoice->order_id,
                'amount'          => $invoice->amount,
                'status'          => $invoice->status,
                'issued_at'       => $invoice->issued_at,
                'created_at'      => $invoice->created_at,
            ];
        });

        return response()->json([
            'message' => 'Factures récupérées avec succès.',
            'data'    => $formattedData,
            'meta'    => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'total'        => $invoices->total(),
            ]
        ], 200);
    }


   // Dans app/Http/Controllers/InvoiceController.php

public function download($invoiceId)
{
    $invoice = Invoice::findOrFail($invoiceId);

    // Sécurité : Vérifier que la facture appartient bien à l'utilisateur connecté
    if ($invoice->user_id !== auth()->id()) {
        return response()->json(['message' => 'Non autorisé.'], 403);
    }

    if (!$invoice->pdf_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->pdf_path)) {
        return response()->json(['message' => 'Fichier PDF introuvable.'], 404);
    }

    // Récupérer le chemin absolu du fichier
    $filePath = storage_path('app/public/' . $invoice->pdf_path);

    // Retourner le fichier. 
    // (Axios gérera l'en-tête pour savoir si c'est du inline ou attachment)
    return response()->file($filePath);
}
    
}