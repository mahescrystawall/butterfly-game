<?php
namespace App\Services;

use App\Models\FlyingHistory;
use App\Events\GameLastScoreEvent;
use App\Events\GameStatusEvent;
use Carbon\Carbon;

class GameService
{
    private $multiplier = 1.00;
    private $rate = 0.01;
    private $randomEndTime;

    public function startGameWithRestart()
    {
        do {
            $this->startGame();
        } while (true); // Infinite loop for automatic restart
    }

    public function startGame()
    {
        echo "Game Started ...\n";

        // Generate random end time for the game
        $randomNumber = $this->generateRandomEndTime();

        // Broadcast the start of the game
        $this->broadcastGameStatus('start', $this->multiplier);

        // Start the game loop without saving a new game
        $this->gameLoop($randomNumber);
    }

    public function stopAllGames()
    {
        FlyingHistory::where('is_active', true)->update(['is_active' => false]);
    }

    private function stopGame()
    {
        echo "Game Ended ...\n";
        sleep(3);

        // After stopping, create a new game row
        $this->createNewGame();

        // Reset the game state for the new game
        $this->resetGameState();
    }

    private function createNewGame()
    {
        // Create a new game record after the current game ends
        $this->generateRandomEndTime(); // Generate a new random end time for the new game
    }

    private function resetGameState()
    {
        // Reset multiplier and rate for the new game
        $this->multiplier = 1.00;
        $this->rate = 0.01;
    }

    private function gameLoop($randomNumber)
    {
        // Loop until the game ends, checking if the multiplier reaches the final value
        while ($this->multiplier < $randomNumber) {
            $this->multiplier += $this->rate;
            $this->rate += 0.01;

            echo "Current multiplier: " . number_format($this->multiplier, 2) . "\n";

            // Broadcast the updated multiplier during the game
            $this->broadcastGameStatus('Running', $this->multiplier);

            // Check if the multiplier has reached or exceeded the random end time
            if ($this->multiplier >= $randomNumber) {
                $newGame = new FlyingHistory();
                $newGame->final_multiplier = $this->multiplier;
                $newGame->save();
                //event(new GameLastScoreEvent($multiplier));
              // event(new GameLastScoreEvent($this->multiplier));
                echo "Current multiplier: " . $this->multiplier . "\n";
                $gameData = [
                    'final_multiplier' => $this->multiplier,
                    'end_time' => now(),
                ];
                $this->broadcastGameStatus('end', $this->multiplier);

              broadcast(new GameLastScoreEvent($this->multiplier));


                $this->stopGame(); // Stop the game
                break;
            }

            sleep(1);
        }
    }

    private function generateRandomEndTime()
    {
        // Generate a random number between 1 and 100, weighted for shorter or longer game times
        $weight = mt_rand(1, 100) / 100;
        $this->randomEndTime = $weight <= 0.90
            ? mt_rand(1, 5) // Shorter game time (1-5)
            : mt_rand(6, 100); // Longer game time (6-100)

        return $this->randomEndTime;
    }

    /**
     * Broadcast the game status update.
     *
     * @param string $status The status of the game (e.g., 'start', 'in-progress', 'end').
     * @param float $multiplier The current multiplier of the game.
     */
    private function broadcastGameStatus($status, $multiplier)
    {
        event(new GameStatusEvent($status, $multiplier));
    }
}
