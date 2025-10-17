<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TransportationResource;
use App\Services\TransportationServices;
use App\Validators\TransportationValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TransportationController extends Controller
{
    public function __construct(private readonly TransportationServices $trans) {}

    public function all(Request $request): JsonResponse
    {
        return Success(payload: [
            'transportations' => TransportationResource::collection($this->trans->all()),
        ]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = TransportationValidators::find($request->all());

        $trans = $this->trans->find($validator->safe()->integer('transportation_id'));

        return Success(payload: [
            'transportation' => TransportationResource::make($trans),
        ]);
    }
}
