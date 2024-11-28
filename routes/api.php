<?php

use App\Http\Controllers\GameHistoryController;


Route::get('/game-history', [GameHistoryController::class, 'index']);

