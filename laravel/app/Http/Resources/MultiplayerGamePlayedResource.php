<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultiplayerGamePlayedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->user),
            "game" => new GameResource($this->game),
            "pairs" => $this->pairs_discovered,
            "won" => $this->player_won,
        ];
    }
}
