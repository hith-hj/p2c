<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\TransportationServices;
use App\Http\Resources\V1\TransportationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportationController extends Controller
{
    public function __construct(private readonly TransportationServices $trans) {}

    public function all(Request $request): JsonResponse
    {
        try {
            return $this->success(payload: [
                'Transportaions' => TransportationResource::collection($this->trans->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $trans = $this->trans->find($validator->safe()->integer('transportation_id'));

            return $this->success(payload: [
                'Transportaions' => TransportationResource::make($trans),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
