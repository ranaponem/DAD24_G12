<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{ 
    # 10 COINS = 1 EURO
    public const EURO_TO_COIN_RATIO = 10.0;
    public const TYPE_BONUS = 'B';
    public const TYPE_PURCHASE = 'P';
    public const TYPE_INTERNAL = 'I';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_datetime',
        'user_id',
        'game_id',
        'type',
        'euros',
        'brain_coins',
        'payment_type',
        'payment_reference'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function game(): BelongsTo {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
