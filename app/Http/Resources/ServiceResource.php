<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'service_type_id'   => $this->service_type_id,
            'type'              => new ServiceTypeResource($this->whenLoaded('type')),
            'image'             => $this->image ? asset(Storage::url($this->image)) : null,
            'name'              => $this->name,
            'price'             => (float) (string) $this->price,
            'stock'             => $this->stock,
        ];
    }
}
