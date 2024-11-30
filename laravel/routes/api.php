<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Models\User;
use App\Models\Game;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, "login"])->name('login');
Route::post('/users', [UserController::class, 'store'])
    ->can('create', User::class);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class,'refreshtoken']);
    Route::get('/users/me', [UserController::class, 'showMe']);

    # Get All
    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class);
    # Get
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->can('create', User::class);
    # Update complete 
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->can('update', User::class);
    # Update partial
    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->can('update', User::class);
    # Delete user
    Route::delete('/users/{user}', [UserController::class, 'delete'])
        ->can('delete', User::class);

    # Get My
    Route::get('/games/my', [GameController::class, 'showMy']);
});

# Get All
Route::get('/games', [GameController::class, 'index']);
