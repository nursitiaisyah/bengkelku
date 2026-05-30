<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'client_id' => $this->client_id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'subtotal' => (float) (string) $this->subtotal,
            'tax' => (float) (string) $this->tax,
            'total' => (float) (string) $this->total,
            'details' => BookingDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
