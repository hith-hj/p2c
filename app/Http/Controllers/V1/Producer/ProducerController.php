<?php

namespace App\Http\Controllers\V1\Producer;

use App\Http\Controllers\Actions\ProducerActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProducerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProducerController extends Controller
{
    public function __construct(private ProducerActions $producer) {}

    public function all(Request $request)
    {
        try {
            return $this->success(payload: [
                'producers' => ProducerResource::collection($this->producer->all($request)),
            ]);
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function get()
    {
        try {
            return $this->success(payload: [
                'producer' => ProducerResource::make($this->producer->get(auth()->id())),
            ]);
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
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
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
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
        } catch (\Throwable $e) {
            return $this->error(payload: ['errors' => $e->getMessage()]);
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
            $this->producer->update(auth()->user()->badge, $validator->safe()->only(['brand']));

            return $this->success(msg: 'Producer Updated');
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $this->producer->delete(auth()->user()->badge);

            return $this->success(msg: 'Producer Deleted');
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
        }
    }
}
