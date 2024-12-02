<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameTypeRequest;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Http\Resources\GameResource;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\UpdateGameRequest;

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
    $game = new Game();
    $game->fill($request->validated());
    $game->status = Game::STATUS_PENDING;
    $game->save();

    return new GameResource($game);
  }

  public function update(UpdateGameRequest $request, Game $game)
  {
    if($game->status == Game::STATUS_ENDED || $game->status == Game::STATUS_INTERRUPTED)
      return response()->json(['message' => 'Cant update ended or interrupted games'], 404);

    $game->fill($request->validated());

    $game->save();

    return new GameResource($game);
  }

  private function readAttributes(Request $request, $query)
  {    
    if($request->has('board')){
      $board_size = explode("x",$request->query('board'));
      $board = Board::where('board_cols', $board_size[0])->where('board_rows', $board_size[1])->first();
      if($board == null)
        return response()->json(['message' => 'Invalid board size: board not found'], 404);
      $query->where('board_id', $board->id);
    }

    if ($request->has('score_type')){
      
      if($request->query('score_type') === 'time')
        $query->orderBy('total_time', 'asc')->orderBy('total_turns_winner', 'asc');
      else if($request->query('score_type') === 'turns')
        $query->orderBy('total_turns_winner', 'asc')->orderBy('total_time', 'asc');
    }
    
    $type = $request->query('type');
    if ($type != 'A')
      $query->where('type', $type ?? Game::TYPE_SINGLEPLAYER);
  }
}
