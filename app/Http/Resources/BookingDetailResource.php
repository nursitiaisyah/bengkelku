<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingDetailResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'service' => new ServiceResource($this->whenLoaded('services')),
            'price' => (float) (string) $this->price,
            'quantity' => $this->quantity,
            'subtotal' => (float) (string) $this->subtotal,
        ];
    }
}
