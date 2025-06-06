<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Enums\NotificationTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NotificationResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'type' => NotificationTypes::from($this->type ?? 1)->name,
            'status' => $this->status,
            'payload' => json_decode($this->payload),
            'created_at' => $this->created_at,
        ];
    }
}
