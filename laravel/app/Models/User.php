<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const TYPE_ADMIN = 'A';
    public const TYPE_PLAYER = 'P';
    public const BLOCKED = 1;
    public const UNBLOCKED = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'nickname',
        'blocked',
        'photo_filename',
        'brain_coins_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function Transactions(): HasMany {
        return $this->hasMany(Transaction::class, 'user_id', 'id'); 
    }

    public function createdGames(): HasMany
    {
      return $this->hasMany(Game::class, 'created_user_id');
    }
    
    public function wonGames(): HasMany
    {
      return $this->hasMany(Game::class, 'winner_user_id');
    }

    public function multiplayerGamesPlayed(): HasMany
    {
      return $this->HasMany(MultiplayerGamePlayed::class, 'game_id', 'id');
    }
}
