<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\FeeResource;
use App\Http\Services\FeeServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    public function __construct(public readonly FeeServices $fee) {}

    public function all(): JsonResponse
    {
        $fees = $this->fee->get(Auth::user()->badge);

        return Success(payload: ['fees' => FeeResource::collection($fees)]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fee_id' => ['required', 'exists:fees,id'],
        ]);

        $fee = $this->fee->find($validator->safe()->integer('fee_id'));

        return Success(payload: ['fee' => FeeResource::make($fee)]);
    }
}
