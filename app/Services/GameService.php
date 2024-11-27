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
        echo "Game Started ..." ;
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
        echo "Game Ended ..." ;
        sleep(5);
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

            echo "Current multiplier: " . number_format($this->multiplier, 2) . PHP_EOL;

            // Check if the multiplier has reached or exceeded the random end time
            if ($this->multiplier >= $randomNumber) {
                $newGame = new FlyingHistory();
                $newGame->final_multiplier = $this->multiplier;
                $newGame->save();
                $gameData = [
                    'final_multiplier' => $this->multiplier,
                    'end_time' => now(),
                ];
                broadcast(new GameEndEvent($gameData));
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
}
