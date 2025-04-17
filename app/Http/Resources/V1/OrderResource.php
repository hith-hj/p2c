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
            'producer' => $this->producer,
            'carrier' => $this->carrier,
            'customer_name' => $this->customer_name,
            'src_long' => (float) $this->src_long,
            'src_lat' => (float) $this->src_lat,
            'dist_long' => (float) $this->dist_long,
            'dist_lat' => (float) $this->dist_lat,
            'distance' => (int) $this->distance,
            'delivery_type' => $this->delivery_type,
            'weight' => $this->weight,
            'cost' => (int) $this->cost,
            'status' => (int) $this->status,
            'created_at' => $this->created_at,
            'picked_at' => $this->picked_at,
            'deliverd_at' => $this->deliverd_at,
        ];
    }
}
