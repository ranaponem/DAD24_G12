<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameTypeRequest;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Http\Resources\GameResource;

class GameController extends Controller
{
  public function index(GameTypeRequest $request)
  {
    $query = Game::where('status', 'E');

    $attributesResponse = $this->readAttributes($request, $query);

    return $attributesResponse ?? GameResource::collection($query->paginate(10));
  }

  public function showMy(GameTypeRequest $request)
  {
    $userId = $request->user()->id;
    $type = $request->query('type') ?? 'S';

    $query = Game::when($type == 'S', function ($query) use ($userId) {
      return $query->where('created_user_id', $userId)->where('type', 'S');
    })->when($type == 'A' || $type == 'M', function ($query) use ($userId) {
      return $query->where('winner_user_id', $userId)
                ->orWhere('created_user_id', $userId);
    })->when($type == 'M', function ($query) use ($userId) {
      return $query->where('type', 'M');
    })->where('status', 'E');

    $attributesResponse = $this->readAttributes($request, $query);

    return $attributesResponse ?? GameResource::collection($query->get());
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
        $query->orderBy('total_time', 'asc');
      else if($request->query('score_type') === 'turns')
        $query->orderBy('total_turns_winner', 'asc');
    }
    
    $type = $request->query('type');
    if ($type != 'A')
      $query->where('type', $type ?? 'S');
  }
}
