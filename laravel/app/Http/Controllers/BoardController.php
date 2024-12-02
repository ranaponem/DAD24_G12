<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BoardResource;
use App\Models\Board;

class BoardController extends Controller
{
  public function index()
  {
    return BoardResource::collection(Board::get());
  }

  public function show(Board $board)
  {
    return new BoardResource($board);
  }
}
