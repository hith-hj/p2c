<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProducerResource;
use App\Http\Services\ProducerServices;
use App\Http\Validators\ProducerValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $this->producer->paginate($request),
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
        $validator = ProducerValidators::find($request->all());

        $producer = $this->producer->find($validator->safe()->integer('producer_id'));

        return Success(payload: [
            'producer' => ProducerResource::make($producer),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = ProducerValidators::create($request->all());

        $producer = $this->producer->create(Auth::user(), $validator->safe()->all());

        return Success(
            payload: ['producer' => ProducerResource::make($producer)],
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validator = ProducerValidators::update($request->all());

        $producer = Auth::user()->badge;
        if ($producer === null) {
            return Error(msg: 'missing producer');
        }

        $this->producer->update($producer, $validator->safe()->only(['brand']));

        return Success(
            msg: __('main.updated'),
            payload: ['producer' => ProducerResource::make($producer->fresh())],
        );
    }

    public function delete(Request $request): JsonResponse
    {
        $producer = Auth::user()->badge;
        if ($producer === null) {
            return Error(msg: 'missing producer');
        }

        $this->producer->delete($producer);

        return Success(msg: __('main.deleted'));
    }
}
