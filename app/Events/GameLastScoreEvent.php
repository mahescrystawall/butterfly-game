<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class GameLastScoreEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, Queueable;

    public $multiplier;

    /**
     * Constructor to initialize the multiplier (final score).
     *
     * @param float $multiplier
     */
    public function __construct($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    /**
     * Define the broadcast channel.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        console.log("hi");
        return new Channel('gameStatus');
    }

    /**
     * Data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'multiplier' => $this->multiplier,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'game-last-score';  // You can customize this event name if needed
    }
}
