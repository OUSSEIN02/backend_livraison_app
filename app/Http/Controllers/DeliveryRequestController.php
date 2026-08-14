<?php
// app/Http/Controllers/Api/DeliveryRequestController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderDeliveryRequest;
use App\Events\OrderAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryRequestController extends Controller
{
    /**
     * POST /api/delivery-requests/{id}/accept
     */
    public function accept(Request $request, $id)
    {
        $deliveryRequest = OrderDeliveryRequest::with('order')
            ->where('id', $id)
            ->where('livreur_id', auth()->id())
            ->firstOrFail();

        if ($deliveryRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cette demande n\'est plus valide.'
            ], 422);
        }

        if ($deliveryRequest->isExpired()) {
            $deliveryRequest->update(['status' => 'expired']);
            return response()->json([
                'success' => false,
                'message' => 'Cette demande a expiré.'
            ], 422);
        }

        $order = $deliveryRequest->order;

        // ⚠️ Vérifie que la commande n'a pas déjà été assignée
        if ($order->assignation_status === 'assignee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été prise par un autre livreur.'
            ], 409);
        }

        DB::transaction(function () use ($deliveryRequest, $order) {
            // Accepter cette requête
            $deliveryRequest->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Refuser toutes les autres requêtes pour cette commande
            OrderDeliveryRequest::where('order_id', $order->id)
                ->where('id', '!=', $deliveryRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'refused',
                    'responded_at' => now(),
                ]);

            // Assigner la commande
            $order->update([
                'livreur_id' => auth()->id(),
                'assignation_status' => 'assignee',
                'status' => 'acceptee',
                'assigned_at' => now(),
            ]);
        });

        // Broadcast à tout le monde
        broadcast(new OrderAssigned($order));

        return response()->json([
            'success' => true,
            'message' => 'Commande acceptée !',
            'data' => $order->fresh('livreur'),
        ]);
    }

    /**
     * POST /api/delivery-requests/{id}/refuse
     */
    public function refuse(Request $request, $id)
    {
        $deliveryRequest = OrderDeliveryRequest::where('id', $id)
            ->where('livreur_id', auth()->id())
            ->firstOrFail();

        if ($deliveryRequest->status === 'pending') {
            $deliveryRequest->update([
                'status' => 'refused',
                'responded_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Demande refusée.',
        ]);
    }
}