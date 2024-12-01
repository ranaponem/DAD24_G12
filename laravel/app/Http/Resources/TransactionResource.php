<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\GameResource;

class TransactionResource extends JsonResource
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
            "transaction_datetime" => $this->transaction_datetime,
            "user" => new UserResource($this->user),
            "game" => new GameResource($this->game),
            "type" => $this->type,
            "euros" => $this->euros,
            "brain_coins" => $this->brain_coins,
            "payment_type" => $this->payment_type,
            "payment_ref" => $this->payment_reference,
        ];
    }
}