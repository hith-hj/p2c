<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Producer;

use App\Http\Controllers\Actions\ProducerActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProducerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProducerController extends Controller
{
    public function __construct(private readonly ProducerActions $producer) {}

    public function all()
    {
        try {
            return $this->success(payload: [
                'producers' => ProducerResource::collection($this->producer->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function paginate(Request $request)
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

    public function get()
    {
        try {
            return $this->success(payload: [
                'producer' => ProducerResource::make($this->producer->get(auth()->id())),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'producer_id' => ['required', 'exists:producers,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $producer = $this->producer->find($validator->safe()->input('producer_id'));

            return $this->success(payload: [
                'producer' => ProducerResource::make($producer),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:20', 'unique:producers,brand'],
            'phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'coords' => ['required', 'array', 'size:2'],
            'coords.long' => ['required', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'coords.lat' => ['required', 'numeric', 'between:-90,90', 'required_with:longitude'],
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

    public function update(Request $request)
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

    public function delete(Request $request)
    {
        try {
            $this->producer->delete(auth()->user()->badge);

            return $this->success(msg: __('main.deleted'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
