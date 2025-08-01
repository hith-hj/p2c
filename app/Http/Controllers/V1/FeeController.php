<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\FeeResource;
use App\Http\Services\FeeServices;
use App\Http\Validators\FeeValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class FeeController extends Controller
{
    public function __construct(public readonly FeeServices $fee) {}

    public function all(): JsonResponse
    {
        $fees = $this->fee->get(Auth::user()->badge);

        return Success(payload: ['fees' => FeeResource::collection($fees)]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = FeeValidators::find($request->all());

        $fee = $this->fee->find($validator->safe()->integer('fee_id'));

        return Success(payload: ['fee' => FeeResource::make($fee)]);
    }
}
