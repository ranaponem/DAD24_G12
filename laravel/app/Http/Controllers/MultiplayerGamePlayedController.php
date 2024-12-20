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
            ->selectRaw('user_id, SUM(player_won) as total_wins')
            ->groupBy('user_id')
            ->orderByDesc('total_wins')
            ->whereHas('user')
            ->with('user')
            ->limit(5)
            ->get();

        return new MultiplayerGamePlayedResource($topWinners);
    }
}
