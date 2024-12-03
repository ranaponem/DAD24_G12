<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\MultiplayerGamePlayedController;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use App\Models\Transaction;

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
        ->can('create', 'user');
    # Update complete 
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->can('update', 'user');
    # Update partial
    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->can('update', 'user');
    # Delete user
    Route::delete('/users/{user}', [UserController::class, 'delete'])
        ->can('delete', 'user');


    # Get My
    Route::get('/games/my', [GameController::class, 'showMy']);

    # Post
    Route::post('/games', [GameController::class, 'store']);

    # Update partial
    Route::patch('/games/{game}', [GameController::class, 'update']);

    # Get All
    Route::get('/boards', [BoardController::class, 'index']);

    # Get One
    Route::get('/boards/{board}', [BoardController::class, 'show']);

    # Get All
    Route::get('/multiplayer', [MultiplayerGamePlayedController::class, 'index']);

    # Get One
    Route::get('/multiplayer/{multiplayer_game_played}', [MultiplayerGamePlayedController::class, 'show']);


    # Get My
    Route::get('/transactions/my', [TransactionController::class, 'showMy'])
        ->can('my', Transaction::class);
    # Get All
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->can('viewAny', Transaction::class);
    # Get
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->can('view', 'transaction');
    # Create transaction
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->can('create', Transaction::class);
});

# Get All
Route::get('/games', [GameController::class, 'index']);