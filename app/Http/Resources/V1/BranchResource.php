<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at->diffForHumans(),
            'location' => LocationResource::make($this->location),
        ];
    }
}
