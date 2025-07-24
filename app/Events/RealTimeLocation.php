<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RealTimeLocation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location;

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return new Channel('location.'.$this->location->user_id);
    }

    public function broadcastAs()
    {
        return 'location.updated';
    }
}
