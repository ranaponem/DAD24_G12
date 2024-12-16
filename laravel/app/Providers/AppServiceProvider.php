<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\AdminPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-create', function (User $user) {
            return $user->type == User::TYPE_ADMIN;
        });
        
        Gate::define('player-block', function (User $user, User $player) {
            return $user->type == User::TYPE_ADMIN && $player->type == User::TYPE_PLAYER;
        });

        Gate::define('admin-destroy', function (User $user, User $otherUser) {
            return $user->type == User::TYPE_ADMIN && $user->id != $otherUser->id;
        });
    }
}
