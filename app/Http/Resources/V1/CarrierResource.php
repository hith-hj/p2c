<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $collection = $this->documents;
        $docs = $collection->filter( fn($item) => $item->doc_type === 'document');
        $profile = $collection->filter( fn($item) => $item->doc_type === 'profile');
        return [
            'id' => $this->id,
            'name' => "$this->first_name $this->last_name",
            'rate' => $this->rate,
            'is_valid' => $this->is_valid,
            'is_online' => $this->is_online,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at->diffForHumans(),
            'transportation' => $this->transportation,
            'details' => $this->details,
            'documents' => $docs->pluck('url'),
            'profile_image' => $profile->pluck('url'),
        ];
    }
}
