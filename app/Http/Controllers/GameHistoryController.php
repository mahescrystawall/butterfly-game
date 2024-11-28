<?php

namespace App\Http\Controllers;

use App\Models\FlyingHistory;
use Illuminate\Http\Request;

class GameHistoryController extends Controller
{
    // Fetch all game history records from the database
    public function index()
    {

        // You can adjust this query to get the history you want
        $history = FlyingHistory::orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json($history);
    }
}
