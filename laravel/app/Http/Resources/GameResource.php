<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BoardResource;

class GameResource extends JsonResource
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
            "created_by" => new UserResource($this->creator),
            "won_by" => $this->winner ? new UserResource($this->winner) : null,
            "type" => $this->type,
            "status" => $this->status,
            "began_at" => $this->began_at,
            "ended_at" => $this->ended_at,
            "total_time" => $this->total_time,
            "board" => new BoardResource($this->board),
            "total_turns" => $this->total_turns_winner
        ];
    }
}
