<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\V1\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'subject_type' => class_basename($this->subject_type),
            'amount' => (int) $this->amount,
            'delay_fee' => (int) $this->delay_fee,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'subject' => $this->when(
                $this->subject_type === Order::class,
                OrderResource::make($this->whenLoaded('subject'))
            ),
        ];
    }
}
