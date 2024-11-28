<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class GameLastScoreEvent implements ShouldBroadcastNow
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


}
