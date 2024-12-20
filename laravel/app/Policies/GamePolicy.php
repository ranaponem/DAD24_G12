<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->type == User::TYPE_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Game $game): bool
    {
        return $user->type == User::TYPE_ADMIN || $user->id == $game->created_user_id || $user->id == $game->created_user_id;
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
        return $user->type == User::TYPE_PLAYER;
    }
}
