<?php

use App\Http\Middleware\CheckSanctumToken;
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

    # Get All
    Route::get('/admins', [UserController::class, 'indexAdmins'])
        ->can('viewAny', User::class);

    # Get
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->can('view', 'user');

    # Special Get (balance only)
    Route::get('/users/mybalance', [UserController::class, 'showMyBalance']);

    # Update complete
    Route::put('/users/{user}', [UserController::class, 'update'])
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

    # Get All
    Route::get('/statistics/profit', [StatisticsController::class, 'totalProfit'])
        ->can('isAdmin');

    Route::get('/statistics/detailed-profit', [StatisticsController::class, 'detailedProfit'])
        ->can('isAdmin');

    Route::get('/statistics/total-users', [StatisticsController::class, 'totalUsers'])
        ->can('isAdmin');

    Route::get('/statistics/total-games', [StatisticsController::class, 'totalGames'])
        ->can('isAdmin');
});

# Get All
Route::get('/games', [GameController::class, 'index']);
