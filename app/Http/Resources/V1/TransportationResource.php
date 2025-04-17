<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportationResource extends JsonResource
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
            'name' => $this->name,
            'capacity' => $this->capacity,
            'cost_per_km' => $this->cost_per_km,
            'cost_per_kg' => $this->cost_per_kg,
            'inital_cost' => $this->inital_cost,
            'cancel_cost' => $this->cancel_cost,
            'category' => $this->category,
        ];
    }
}
