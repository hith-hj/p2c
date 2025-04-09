<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProducerResource extends JsonResource
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
            'brand' => $this->brand,
            'is_valid' => (int) $this->is_valid,
            'rate' => (int) $this->rate,
            'created_at' => $this->created_at->diffForHumans(),
            // 'branches'=>BranchResource::collection($this->branches),
        ];
    }
}
