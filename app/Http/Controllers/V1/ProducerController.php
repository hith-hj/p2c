<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProducerResource;
use App\Http\Services\ProducerServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProducerController extends Controller
{
    public function __construct(private readonly ProducerServices $producer) {}

    public function all(): JsonResponse
    {
        return Success(payload: [
            'producers' => ProducerResource::collection($this->producer->all()),
        ]);
    }

    public function paginate(Request $request): JsonResponse
    {
        return Success(payload: [
            'producers' => ProducerResource::collection(
                $this->producer->paginate($request)
            ),
        ]);
    }

    public function get(): JsonResponse
    {
        return Success(payload: [
            'producer' => ProducerResource::make($this->producer->get(Auth::id())),
        ]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'producer_id' => ['required', 'exists:producers,id'],
        ]);
        $producer = $this->producer->find($validator->safe()->integer('producer_id'));

        return Success(payload: [
            'producer' => ProducerResource::make($producer),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:20', 'unique:producers,brand'],
            'phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
        $producer = $this->producer->create(Auth::user(), $validator->safe()->all());

        return Success(
            payload: ['producer' => ProducerResource::make($producer)]
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'unique:producers,brand'],
        ]);
        $producer = Auth::user()->badge;
        $this->producer->update($producer, $validator->safe()->only(['brand']));

        return Success(
            msg: __('main.updated'),
            payload: ['producer' => ProducerResource::make($producer->fresh())]
        );
    }

    public function delete(Request $request): JsonResponse
    {
        $this->producer->delete(Auth::user()->badge);

        return Success(msg: __('main.deleted'));
    }
}
