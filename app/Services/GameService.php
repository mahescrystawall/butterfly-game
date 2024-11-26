<?php

namespace App\Services;

use App\Models\FlyingHistory;
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
        // Only generate the random end time, do not create a new game instance here
        $randomNumber = $this->generateRandomEndTime();

        // Start the game loop without saving a new game
        $this->gameLoop($randomNumber);
    }

    public function stopAllGames()
    {
        FlyingHistory::where('is_active', true)->update(['is_active' => false]);
    }

    private function stopGame()
    {
        // After stopping, create a new game row
        $this->createNewGame();

        // Reset the game state for the new game
        $this->resetGameState();
    }

    private function createNewGame()
    {
        // Create a new game record after the current game ends
        $this->generateRandomEndTime(); // Generate a new random end time for the new game
        $newGame = new FlyingHistory();
        $newGame->final_multiplier = $this->randomEndTime;
        $newGame->save();

        echo "New game created: " . $newGame->id . PHP_EOL;
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

            echo "Current multiplier: " . number_format($this->multiplier, 2) . PHP_EOL;

            // Check if the multiplier has reached or exceeded the random end time
            if ($this->multiplier >= $randomNumber) {
                $this->stopGame(); // Stop the game
                break;
            }

            sleep(1); // Pause for a second before the next loop iteration
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
}
