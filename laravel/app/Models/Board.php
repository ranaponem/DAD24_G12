<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
  use HasFactory;

    protected $fillable = [
        'board_cols',
        'board_rows',
    ];

  public $timestamps = false;

  public function games() : HasMany
  {
    return $this.hasMany(Game::class);
  }
}
