<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Producer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ProducerServices;
use App\Http\Resources\V1\ProducerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProducerController extends Controller
{
    public function __construct(private readonly ProducerServices $producer) {}

    public function all(): JsonResponse
    {
        try {
            return $this->success(payload: [
                'producers' => ProducerResource::collection($this->producer->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function paginate(Request $request): JsonResponse
    {
        try {
            return $this->success(payload: [
                'producers' => ProducerResource::collection(
                    $this->producer->paginate($request)
                ),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function get(): JsonResponse
    {
        try {
            return $this->success(payload: [
                'producer' => ProducerResource::make($this->producer->get(auth()->id())),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'producer_id' => ['required', 'exists:producers,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $producer = $this->producer->find($validator->safe()->integer('producer_id'));

            return $this->success(payload: [
                'producer' => ProducerResource::make($producer),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:20', 'unique:producers,brand'],
            'phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'coords' => ['required', 'array', 'size:2'],
            'coords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'coords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $producer = $this->producer->create(auth()->user(), $validator->safe()->all());

            return $this->success(
                payload: ['producer' => ProducerResource::make($producer)]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'unique:producers,brand'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $producer = auth()->user()->badge;
            $this->producer->update($producer, $validator->safe()->only(['brand']));

            return $this->success(msg: __('main.updated'), payload: ['producer' => ProducerResource::make($producer->fresh())]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->producer->delete(auth()->user()->badge);

            return $this->success(msg: __('main.deleted'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
