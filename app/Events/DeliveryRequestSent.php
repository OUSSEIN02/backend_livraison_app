<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class DeliveryRequestSent implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(
        public Order $order,
        public $livreurs
    ) {}

    // Un canal privé par livreur → chaque livreur reçoit SA notification
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->livreurs as $livreur) {
            $channels[] = new PrivateChannel('livreur.' . $livreur->id);
        }
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'delivery.request';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'pickup_name' => $this->order->pickup_name,
            'pickup_address' => $this->order->pickup_address,
            'dropoff_address' => $this->order->dropoff_address,
            'distance_km' => $this->order->distance_km,
            'tarif_total' => $this->order->tarif_total,
            'expires_in' => 25, // secondes
        ];
    }
}