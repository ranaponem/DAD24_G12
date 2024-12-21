<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMultiplayerGameRequest;
use App\Http\Requests\UpdateMultiplayerGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\MultiplayerGamePlayedResource;
use App\Models\Game;
use App\Models\MultiplayerGamePlayed;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\MultiplayerGamePlayedPolicy;
use Carbon\Carbon;
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

  public function store(CreateMultiplayerGameRequest $request)
  {
    $requestValidated = $request->validated();
    $player2 = $request->user();

    if ($player2->brain_coins_balance - 5 < 0)
      return response()->json(['message' => 'Insufficient balance.'], 400);
    
    $player1 = User::find($requestValidated['player_1_id']);
    if ($player1->brain_coins_balance - 5 < 0)
      return response()->json(['message' => 'Insufficient balance for player 2.'], 400);

    $time = now();

    $game = DB::transaction(function () use ($requestValidated, $player2, $player1, $time) {

      $game = new Game();
      $game->fill($requestValidated);
      $game->began_at = $time;
      $game->type = Game::TYPE_MULTIPLAYER;
      $game->created_user_id = $player2->id;
      
      $game->status = Game::STATUS_PROGRESS;

      $game->save();

      $transaction1 = $this->gameTransaction($player2->id, $game->id, Transaction::TYPE_INTERNAL, -5);
      $transaction2 = $this->gameTransaction($player1->id, $game->id, Transaction::TYPE_INTERNAL, -5);
      $this->makeMultiplayerRecord($player2->id, $game->id);
      $this->makeMultiplayerRecord($player1->id, $game->id);

      $player2->brain_coins_balance += $transaction1->brain_coins;
      $player2->save(); 
      
      $player1->brain_coins_balance += $transaction2->brain_coins;
      $player1->save();

      return $game;
    });

    $game->began_at = $time->isoFormat("YYYY-mm-DD HH:MM:ss");

    return new GameResource($game);
  }

  public function update(UpdateMultiplayerGameRequest $request, Game $game)
  {
    $user = $request->user();
    $policy = new MultiplayerGamePlayedPolicy();

    if (!$policy->update($user, $game)) {
        return abort(403, 'This action is unauthorized.');
    }
    
    if($game->status == $request->status)
      return response()->json(['message' => 'Game is already in that state'], 400);

    if($game->winner_user_id != null && ($game->status == Game::STATUS_ENDED || $game->status == Game::STATUS_INTERRUPTED))
      return response()->json(['message' => 'Cannot update ended or interrupted games'], 400);

    $requestValidated = $request->validated();

    $multiPlayerRecord = MultiplayerGamePlayed::where('game_id', $game->id)->where('user_id', $user->id)->first();

    $game = DB::transaction(function () use ($user, $game, $multiPlayerRecord, $requestValidated) {
      if($requestValidated['my_win'] == 1) {
        $game->fill($requestValidated);
        $game->ended_at = now();
        $game->total_time = Carbon::parse($game->ended_at)->diffInSeconds(Carbon::parse($game->began_at));

        $game->winner_user_id = $user->id;
        $game->save();

        $multiPlayerRecord->player_won = 1;

        $transaction = $this->gameTransaction($user->id, $game->id, Transaction::TYPE_INTERNAL, 7);
        
        $user->brain_coins_balance += $transaction->brain_coins;
        $user->save();
      }

      if ($requestValidated['status'] == Game::STATUS_ENDED)
        $multiPlayerRecord->pairs_discovered = $requestValidated['pairs'];
      
      $multiPlayerRecord->save();
      return $game;
    });

    return new GameResource($game);
  }
  
  private function gameTransaction($userId, $gameId, $type, $amount)
  {
    $transaction = new Transaction();
    $transaction->user_id = $userId;
    $transaction->game_id = $gameId;
    $transaction->type = $type;
    $transaction->brain_coins = $amount;
    $transaction->transaction_datetime = now();

    $transaction->save();
    return $transaction;
  }

  private function makeMultiplayerRecord($userId, $gameId) { 
      $multiplayer_game = new MultiplayerGamePlayed();
      $multiplayer_game->user_id = $userId;
      $multiplayer_game->game_id = $gameId;
      $multiplayer_game->player_won = 0;
      $multiplayer_game->save();
  }
}
