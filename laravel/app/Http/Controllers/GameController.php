<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameTypeRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Http\Resources\GameResource;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Carbon\Carbon;

class GameController extends Controller
{
  public function index(GameTypeRequest $request)
  {
    $query = Game::where('status', Game::STATUS_ENDED);

    $attributesResponse = $this->readAttributes($request, $query);

    return $attributesResponse ?? GameResource::collection($query->paginate(10));
  }

  public function showMy(GameTypeRequest $request)
  {
    $userId = $request->user()->id;
    $type = $request->query('type') ?? Game::TYPE_SINGLEPLAYER;

    $query = Game::when($type == Game::TYPE_SINGLEPLAYER, function ($query) use ($userId) {
      return $query->where('created_user_id', $userId)->where('type', Game::TYPE_SINGLEPLAYER);
    })->when($type == 'A' || $type == Game::TYPE_MULTIPLAYER, function ($query) use ($userId) {
      return $query->where('winner_user_id', $userId)
                ->orWhere('created_user_id', $userId);
    })->when($type == Game::TYPE_MULTIPLAYER, function ($query) use ($userId) {
      return $query->where('type', Game::TYPE_MULTIPLAYER);
    })->where('status', Game::STATUS_ENDED);

    $attributesResponse = $this->readAttributes($request, $query);

    return $attributesResponse ?? GameResource::collection($query->paginate(10));
  }

  public function store(CreateGameRequest $request)
  {
    $requestValidated = $request->validated();
    $user = $request->user();
    
    if ($user->brain_coins_balance - 1 < 0)
      return response()->json(['message' => 'Insufficient balance.'], 400);

    $time = Carbon::now();
    
    $game = DB::transaction(function () use ($requestValidated, $user, $time) {

      $game = new Game();
      $game->fill($requestValidated);
      $game->created_user_id = $user->id;
      $game->began_at = $time;
      
      match ($game->type) {
        Game::TYPE_SINGLEPLAYER => $game->status = Game::STATUS_PROGRESS,
        Game::TYPE_MULTIPLAYER => $game->status = Game::STATUS_PENDING,
      };

      $game->save();

      if ($game->board_id != 1) {

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->game_id = $game->id;
        $transaction->type = Transaction::TYPE_INTERNAL;
        $transaction->brain_coins = -1;
        $transaction->transaction_datetime = $time;

        $transaction->save();

        $user->brain_coins_balance -= 1;
                
        $user->save();
      }

      return $game;
    });
    
    $game->began_at = $time->isoFormat("YYYY-mm-DD HH:MM:ss");

    return new GameResource($game);
  }

  public function update(UpdateGameRequest $request, Game $game)
  {
    if($game->status == Game::STATUS_ENDED || $game->status == Game::STATUS_INTERRUPTED)
      return response()->json(['message' => 'Cannot update ended or interrupted games'], 400);

    if($game->status == Game::STATUS_PROGRESS && $request->status == Game::STATUS_PENDING)
      return response()->json(['message' => 'Cannot put a game in progress back to pending'], 400);

    $game->fill($request->validated());
    $game->ended_at = Carbon::now();
    
    if ($game->type == Game::TYPE_MULTIPLAYER) {
      $game->winner_user_id = $request->winner_user_id;
    }

    $game->save();

    return new GameResource($game);
  }

  private function readAttributes(Request $request, $query)
  {    
    // Filter by board
    if($request->has('board')){
      $board_size = explode("x",$request->query('board'));
      $board = Board::where('board_cols', $board_size[0])->where('board_rows', $board_size[1])->first();
      if($board == null)
        return response()->json(['message' => 'Invalid board size: board not found'], 404);
      $query->where('board_id', $board->id);
    }

    // Order by either number of turns or time taken (score)
    $isOrderedByScore = false;
    if ($request->has('score_type')){
      if($request->query('score_type') === 'time'){
        $query->orderBy('total_time', 'asc');
        $isOrderedByScore = true;
      }
      else if($request->query('score_type') === 'turns'){
        $query->orderBy('total_turns_winner', 'asc');
        $isOrderedByScore = true;
      }
    }

    // Filter by type, defaults to singleplayer
    $type = $request->query('type');
    if ($type != 'A')
      $query->where('type', $type ?? Game::TYPE_SINGLEPLAYER);

    // Filter by a start date (all games played after it are gotten)
    if($request->has('date_start')){
      $dateStart = $request->query('date_start');
      $parsedDateStart = \DateTime::createFromFormat('d-m-Y', $dateStart);

      if($parsedDateStart)
          $query->where('ended_at', '>=', $parsedDateStart->format('Y-m-d'));

      else
        return response()->json(['message' => 'Invalid \'date_start\' date format: must be DD-mm-YYYY'], 402);
    }

    // Filter by end date (all games played before it are gotten)
    if($request->has('date_end')){
      $dateEnd = $request->query('date_end');
      $parsedDateEnd = \DateTime::createFromFormat('d-m-Y', $dateEnd);

      if($parsedDateEnd)
        $query->where('ended_at', '<=', $parsedDateEnd->format('Y-m-d').' 23:59:59');

      else
        return response()->json(['message' => 'Invalid \'date_end\' date format: must be DD-mm-YYYY'], 402);
    }
    // Ordering by most recent or by oldest if score ordering was parsed
    $query->orderBy('ended_at', $isOrderedByScore ? 'asc' : 'desc');
  }
}
