<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'producer' => $this->producer?->badge?->brand,
            'carrier' => $this->carrier?->badge?->first_name,
            'transportation' => $this->transportation?->name,
            'customer_name' => $this->customer_name,
            'src_long' => (float) $this->src_long,
            'src_lat' => (float) $this->src_lat,
            'dest_long' => (float) $this->dest_long,
            'dest_lat' => (float) $this->dest_lat,
            'distance' => (int) $this->distance,
            'delivery_type' => $this->delivery_type,
            'weight' => $this->weight,
            'cost' => (int) $this->cost,
            'status' => (int) $this->status,
            'created_at' => $this->created_at->diffForHumans(),
            'picked_at' => $this->picked_at,
            'deliverd_at' => $this->deliverd_at,
            'attrs' => $this->attrs->pluck('name'),
            'items' => $this->items->pluck('name'),
        ];
    }
}
