<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->type == User::TYPE_ADMIN;
    }

    /**
     * Determine whether the user can view its transactions.
     */
    public function my(User $user): bool
    {
        return $user->type == User::TYPE_PLAYER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->type == User::TYPE_ADMIN || $user->id === $transaction->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type == User::TYPE_PLAYER;
    }
}
