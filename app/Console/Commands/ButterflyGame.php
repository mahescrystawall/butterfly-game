<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FlyingHistory;
use App\Services\GameService;

class ButterflyGame extends Command
{
    protected $signature = 'game:butterfly {action}';
    protected $description = 'Start or stop the butterfly game.';
    private $gameService;

    public function __construct(GameService $gameService)
    {
        parent::__construct();
        $this->gameService = $gameService;
    }

    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'start') {
            $this->info('Starting the game...');
            $this->gameService->startGameWithRestart();
        } elseif ($action === 'stop') {
            $this->info('Stopping the game...');
            $this->gameService->stopAllGames();
        } else {
            $this->error('Invalid action. Use "start" or "stop".');
        }
    }
}
