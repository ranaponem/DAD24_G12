<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Http\Resources\GameResource;

class GameController extends Controller
{
  public function index(Request $request)
  {
    $query = Game::where('status', 'E');

    $this->readAttributes($request, $query);

    return GameResource::collection($query->paginate(10));
  }

  public function showMy(Request $request)
  {
    $userId = $request->user()->id;

    $query = Game::where(function ($query) use ($userId) {
        $query->where('created_user_id', $userId)
              ->orWhere('winner_user_id', $userId);
    })->where('status', 'E');

    $attributesResponse = $this->readAttributes($request, $query);

    if($attributesResponse != null)
      return $attributesResponse;

    return GameResource::collection($query->paginate(10));
  }

  private function readAttributes(Request $request, $query)
  {
    if($request->has('board')){
      switch($request->query('board')){
      case '4x3':
        $query->where('board_id', Board::where('board_cols', 3)->where('board_rows', 4)->first()->id);
        break;
      case '4x4':
        $query->where('board_id', Board::where('board_cols', 4)->where('board_rows', 4)->first()->id);
        break;
      case '6x6':
        $query->where('board_id', Board::where('board_cols', 6)->where('board_rows', 6)->first()->id);
        break;
      default:
        return response()->json(['message' => 'Invalid board configuration'], 404);
      }
    }

    if ($request->has('score_type')){

      if($request->query('score_type') === 'time')
        $query->orderBy('total_time', 'asc'); 

      else if($request->query('score_type') === 'turns')
        $query->orderBy('total_turns_winner', 'asc');

      else
        return response()->json(['message' => 'Invalid score type: accepted values are \'time\' and \'turns\''], 404);
    }
  }
}
