<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\MultiplayerGamePlayed;
use App\Models\User;

class MultiplayerGamePlayedPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MultiplayerGamePlayed $multiplayerGamePlayed): bool
    {
        return $user->type == User::TYPE_ADMIN || $user->id == $multiplayerGamePlayed->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type == User::TYPE_PLAYER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Game $game): bool
    {
        return $user->type == User::TYPE_PLAYER &&
            MultiplayerGamePlayed::where('user_id', $user->id)->where('game_id', $game->id)->first() != null;
    }
}
