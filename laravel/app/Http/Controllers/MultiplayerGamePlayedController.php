<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MultiplayerGamePlayedResource;
use App\Models\MultiplayerGamePlayed;

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
}
