<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_user_id',
        'winner_user_id',
        'type',
        'status',
        'began_at',
        'ended_at',
        'total_time',
        'board_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id', 'id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'game_id', 'id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id', 'id');
    }

    public function multiplayerGamesPlayed(): HasMany
    {
        return $this->HasMany(MultiplayerGamePlayed::class, 'game_id', 'id');
    }
}
