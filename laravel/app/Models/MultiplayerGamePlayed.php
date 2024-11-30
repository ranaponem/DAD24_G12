<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MultiplayerGamePlayed extends Model
{
  use HasFactory;

  public $timestamps = false;
  protected $table = 'multiplayer_games_played';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'game_id',
        'player_won',
        'pairs_discovered',
    ];

  public function user() : BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function game() : BelongsTo
  {
    return $this->belongsTo(Game::class, 'game_id', 'id');
  }
}
