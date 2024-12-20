<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MultiplayerGamePlayedResource;
use App\Models\MultiplayerGamePlayed;
use Illuminate\Support\Facades\DB;

class MultiplayerGamePlayedController extends Controller
{
    public function index()
    {
        return MultiplayerGamePlayedResource::collection(MultiplayerGamePlayed::get());
    }

    public function show(int $id)
    {
        return new MultiplayerGamePlayedResource(MultiplayerGamePlayed::find($id));
    }

    public function topfiveplayers()
    {
        $topWinners = MultiplayerGamePlayed::where('player_won', 1)
            ->selectRaw('user_id, SUM(player_won) as total_wins') // Select user_id and sum of player_won
            ->groupBy('user_id') // Group by user_id
            ->orderByDesc('total_wins') // Order by the total_wins in descending order
            ->whereHas('user') // Ensure the user relationship exists
            ->with('user') // Eager load the 'user' relationship
            ->limit(5) // Limit to top 5
            ->get();

        return new MultiplayerGamePlayedResource($topWinners);
    }
}
