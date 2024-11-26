<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FlyingHistory;
use Carbon\Carbon;

class ButterflyGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:butterfly {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start or stop the butterfly game.';

    private $multiplier = 1.0;

    public function handle()
    {
        $action = $this->argument('action');

        // Ensure no active games exist before starting a new one
        FlyingHistory::where('is_active', true)->update(['is_active' => false]);

        $game = FlyingHistory::where('is_active', true)->latest()->first();

        if ($action === 'start') {
            if ($game) {
                $this->error('A game is already active!');
                return;
            }

            $this->startGame();
        } elseif ($action === 'stop') {
            if (!$game || !$game->is_active) {
                $this->error('No active game to stop!');
                return;
            }

            $this->stopGame($game);
        } else {
            $this->error('Invalid action. Use "start" or "stop".');
        }
    }

    private function startGame()
    {
        $game = new FlyingHistory();
        $game->start_time = Carbon::now();
        $game->is_active = true;
        $game->save();

        $this->info('Game started.');

        $this->gameLoop($game);
    }

    private function stopGame(FlyingHistory $game)
    {
        $game->end_time = Carbon::now();
        $game->is_active = false;
        $game->final_multiplier = $this->multiplier;
        $game->save();

        $this->info('Game stopped.');
        $this->info("Final multiplier: " . number_format($this->multiplier, 2));

        // Restart the game after stopping
        $this->initializeGame();
    }

    private function gameLoop(FlyingHistory $game)
    {
        while ($game->is_active) {
            $this->multiplier += 0.01;
            $this->info("Current multiplier: " . number_format($this->multiplier, 2));

            if ($this->checkEndGameCondition($game)) {
                $this->stopGame($game);
                break;
            }

            sleep(1);
            $game->refresh();
        }
    }

    private function checkEndGameCondition(FlyingHistory $game): bool
    {
        return Carbon::parse($game->start_time)->diffInSeconds(Carbon::now()) >= 10;
    }

    /**
     * Restart the game after it ends.
     */
    private function initializeGame()
    {
        $this->info("Restarting the game in 5 seconds...");
        sleep(5);

        $this->multiplier = 1.0; // Reset multiplier

        $game = new FlyingHistory();
        $game->start_time = Carbon::now();
        $game->is_active = true;
        $game->save();

        $this->info("New game session started.");
        $this->gameLoop($game);
    }
}
