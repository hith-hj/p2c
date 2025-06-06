<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ReviewResource extends JsonResource
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
            'reviewer' => ['type' => $this->reviewer::class, 'id' => $this->reviewer->id],
            'rate' => (float) $this->rate,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}
