<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Carrier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\CarrierServices;
use App\Http\Resources\V1\CarrierResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarrierController extends Controller
{
    public function __construct(private readonly CarrierServices $carrier) {}

    public function all(): JsonResponse
    {
        try {
            return $this->success(payload: [
                'carriers' => CarrierResource::collection($this->carrier->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function paginate(Request $request): JsonResponse
    {
        try {
            return $this->success(payload: [
                'carriers' => CarrierResource::collection(
                    $this->carrier->paginate($request)
                ),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => ['required', 'exists:carriers,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $carrier = $this->carrier->find($validator->safe()->integer('carrier_id'));

            return $this->success(payload: [
                'carrier' => CarrierResource::make($carrier),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function get(): JsonResponse
    {
        try {
            return $this->success(payload: [
                'carrier' => CarrierResource::make($this->carrier->get(auth()->id())),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $carrier = $this->carrier->create(auth()->user(), $validator->safe()->all());

            return $this->success(
                payload: ['carrier' => CarrierResource::make($carrier)]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function createDetails(Request $request): JsonResponse
    {
        if (auth()->user()->badge === null) {
            return $this->error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = Validator::make($request->all(), [
            'plate_number' => ['required', 'numeric', 'unique:carrier_details,plate_number'],
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year' => ['required', 'date_format:Y'],
            'color' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $carrier = $this->carrier->find(auth()->user()->badge->id);
            $details = $this->carrier->createDetails($carrier, $validator->safe()->all());

            return $this->success(
                payload: ['carrier' => CarrierResource::make($carrier->fresh())]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function createDocuments(Request $request): JsonResponse
    {
        if (auth()->user()->badge === null) {
            return $this->error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array', 'size:5'],
            'images.*' => ['required', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $carrier = $this->carrier->find(auth()->user()->badge->id);
            $this->carrier->createDocuments($carrier, $validator->safe()->input('images'));

            return $this->success(
                payload: ['carrier' => CarrierResource::make($carrier->fresh())]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'transportation_id' => ['sometimes', 'exists:transportations,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $carrier = $this->carrier->find(auth()->user()->badge->id);
            if ($validator->safe()->exists('transportation_id')) {
                if ($carrier->details()->count() > 0) {
                    $carrier->details()->delete();
                }

                $carrier->validate(false);
            }

            $this->carrier->update($carrier, $validator->safe()->all());

            return $this->success(msg: __('main.updated'), payload: [
                'carrier' => CarrierResource::make($carrier->fresh()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->carrier->delete(auth()->user()->badge);

            return $this->success(msg: __('main.deleted'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function setLocation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $res = $this->carrier->setLocation(auth()->user()->badge, $validator->safe()->all());

            return $this->success(msg: __('main.Location updated'), payload: [$res]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
