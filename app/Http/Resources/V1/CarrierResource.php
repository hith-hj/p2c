<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CarrierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $collection = $this->images;
        $docs = $collection->filter(fn ($item): bool => $item->type === 'document');
        $profile = $collection->filter(fn ($item): bool => $item->type === 'profile');

        return [
            'id' => $this->id,
            'name' => sprintf('%s %s', $this->first_name, $this->last_name),
            'rate' => $this->rate,
            'is_valid' => (int) $this->is_valid,
            'is_online' => (int) $this->is_online,
            'is_available' => (int) $this->is_available,
            'created_at' => $this->created_at,
            'transportation' => $this->transportation,
            'details' => $this->details,
            'documents' => $docs->pluck('url')->map(fn ($url) => asset($url)),
            'profile_image' => $profile->pluck('url')->map(fn ($url) => asset($url)),
            'is_filled' => (int) (! $this->images->isEmpty() && $this->details !== null && $this->transportation !== null),
        ];
    }
}
