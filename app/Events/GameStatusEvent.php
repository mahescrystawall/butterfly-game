<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class GameStatusEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameStatus;
    public $multiplier;

    /**
     * Create a new event instance.
     */
    public function __construct($gameStatus, $multiplier)
    {
        $this->gameStatus = $gameStatus;
        $this->multiplier = $multiplier;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('gameStatus');
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'gameStatus' => $this->gameStatus,
            'multiplier' => $this->multiplier,
        ];
    }
}
