<?php

use App\Http\Controllers\AdminController;
use App\Http\Middleware\CheckSanctumToken;
use App\Models\Game;
use App\Models\MultiplayerGamePlayed;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\MultiplayerGamePlayedController;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Board;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, "login"])->name('login');


Route::post('/users', [UserController::class, 'store'])
    ->can('create', User::class);

# Get All
Route::get('/boards', [BoardController::class, 'index'])
    ->can('viewAny', Board::class);
# Get One
Route::get('/boards/{board}', [BoardController::class, 'show'])
    ->can('view', 'board');


Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class,'refreshtoken']);
    Route::get('/users/me', [UserController::class, 'showMe']);

    # Get All Players
    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class);

    # Special Get (balance only)
    Route::get('/users/mybalance', [UserController::class, 'showMyBalance']);

    # Get All
    Route::get('/admins', [UserController::class, 'indexAdmins'])
        ->can('viewAny', User::class);

    # Get
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->can('view', 'user');

    # Update my user
    Route::put('/users/me', [UserController::class, 'update']);

    # Update my password
    Route::put('/users/updatepassword', [UserController::class, 'updatePassword']);

    # Delete me
    Route::delete('/users/me', [UserController::class, 'destroy'])
        ->can('delete', User::class);



    # Get My
    Route::get('/games/my', [GameController::class, 'showMy']);

    # Post
    Route::post('/games', [GameController::class, 'store']);

    # Update partial
    Route::patch('/games/{game}', [GameController::class, 'update'])
        ->can('update', 'game');

    # Get All
    Route::get('/multiplayer', [MultiplayerGamePlayedController::class, 'index']);

    # Get One
    Route::get('/multiplayer/{multiplayer_game_played}', [MultiplayerGamePlayedController::class, 'show']);

    # Create Multiplayer game
    Route::post('/multiplayer', [MultiplayerGamePlayedController::class, 'store'])
        ->can('create', MultiplayerGamePlayed::class);

    # Update multiplayer
    Route::patch('/multiplayer/{game}', [MultiplayerGamePlayedController::class, 'update']);

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

    # Create new admin
    Route::post('/admins', [AdminController::class, 'store'])
        ->can('admin-create');

    # Update player blocked state
    Route::put('/users/{user}/changeblock', [AdminController::class, 'updatePlayerState'])
        ->can('player-block', 'user');

    # Delete user
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])
        ->can('admin-destroy', 'user');


    # Get All
    Route::get('/statistics/profit', [StatisticsController::class, 'totalProfit'])
        ->can('isAdmin');

    Route::get('/statistics/detailed-profit', [StatisticsController::class, 'detailedProfit'])
        ->can('isAdmin');

});

Route::get('/statistics/total-users', [StatisticsController::class, 'totalUsers']);

Route::get('/statistics/total-games', [StatisticsController::class, 'totalGames']);

# Get All
Route::get('/games', [GameController::class, 'index']);

Route::get('/gamesended', [GameController::class, 'indexEndedGames']);

# Get Top 5
Route::get('/topfivemultiplayer', [MultiplayerGamePlayedController::class, 'topfiveplayers']);
