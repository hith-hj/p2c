<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Fee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\FeeServices;
use App\Http\Resources\V1\FeeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    public function __construct(public readonly FeeServices $fee) {}

    public function all(): JsonResponse
    {
        try {
            $fees = Auth::user()->badge->fees()->withOut(['subject'])->get();

            return $this->success(payload: [
                'fees' => FeeResource::collection($fees),
            ]);
        } catch (\Exception $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fee_id' => ['required', 'exists:fees,id'],
        ]);
        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $fee = Auth::user()
                ->badge
                ->fees()
                ->with(['subject', 'holder'])
                ->where('id', $validator->safe()->integer('fee_id'))
                ->first();

            return $this->success(payload: ['fee' => FeeResource::make($fee)]);
        } catch (\Exception $e) {
            return $this->error(msg: $e->getMessage());
        }
    }
}
