<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderAssigned implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        // Notifie vendeur ET livreur
        return [
            new PrivateChannel('vendeur.' . $this->order->vendeur_id),
            new PrivateChannel('livreur.' . $this->order->livreur_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'livreur' => [
                'id' => $this->order->livreur->id,
                'name' => $this->order->livreur->name,
                'phone' => $this->order->livreur->phone,
            ],
            'tarif_total' => $this->order->tarif_total,
        ];
    }
}