<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            'producer' => $this->producer?->brand,
            'carrier' => $this->carrier?->first_name,
            'transportation' => $this->transportation?->name,
            'customer_name' => $this->customer_name,
            'branch' => $this->branch->only(['id', 'name']),
            'src_long' => (float) $this->src_long,
            'src_lat' => (float) $this->src_lat,
            'dest_long' => (float) $this->dest_long,
            'dest_lat' => (float) $this->dest_lat,
            'distance' => (int) $this->distance,
            'goods_price' => (int) $this->goods_price,
            'delivery_type' => (string) $this->delivery_type,
            'weight' => (int) $this->weight,
            'cost' => (int) $this->cost,
            'status' => (int) $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'picked_at' => $this->picked_at,
            'delivered_at' => $this->delivered_at,
            'dte' => $this->dte,
            'note' => $this->note,
            'attrs' => $this->attrs->select(['id', 'name']),
            'items' => $this->items->select(['id', 'name']),
            'codes' => $this->when(
                Auth::user()->badge->id === $this->producer_id &&
                Auth::user()->role === UserRoles::Producer->value,
                $this->codes()->get(['type', 'code'])
            ),
            'pickup_code' => $this->when(
                Auth::user()->badge->id === $this->carrier_id &&
                Auth::user()->role === UserRoles::Carrier->value,
                $this->codes()->where('type', 'pickup')->get(['type', 'code'])
            ),
        ];
    }
}
