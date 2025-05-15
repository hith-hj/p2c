<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarrierResource;
use App\Http\Services\CarrierServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarrierController extends Controller
{
    public function __construct(private readonly CarrierServices $carrier) {}

    public function all(): JsonResponse
    {
        return Success(payload: [
            'carriers' => CarrierResource::collection($this->carrier->all()),
        ]);
    }

    public function paginate(Request $request): JsonResponse
    {
        return Success(payload: [
            'carriers' => CarrierResource::collection(
                $this->carrier->paginate($request)
            ),
        ]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => ['required', 'exists:carriers,id'],
        ]);

        $carrier = $this->carrier->find($validator->safe()->integer('carrier_id'));

        return Success(payload: ['carrier' => CarrierResource::make($carrier)]);
    }

    public function get(): JsonResponse
    {
        return Success(payload: [
            'carrier' => CarrierResource::make($this->carrier->get(Auth::id())),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);

        $carrier = $this->carrier->create(Auth::user(), $validator->safe()->all());

        return Success(payload: ['carrier' => CarrierResource::make($carrier)]);
    }

    public function createDetails(Request $request): JsonResponse
    {
        if (Auth::user()->badge === null) {
            return Error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = Validator::make($request->all(), [
            'plate_number' => ['required', 'numeric', 'unique:carrier_details,plate_number'],
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year' => ['required', 'date_format:Y'],
            'color' => ['required', 'string'],
        ]);

        $carrier = Auth::user()->badge;
        $details = $this->carrier->createDetails($carrier, $validator->safe()->all());
        if ($carrier->images()->exists()) {
            $carrier->validate(true);
        }

        return Success(
            payload: ['carrier' => CarrierResource::make($carrier->fresh())]
        );
    }

    public function createImages(Request $request): JsonResponse
    {
        if (Auth::user()->badge === null) {
            return Error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array', 'size:5'],
            'images.*' => ['required', 'image', 'max:2048'],
        ]);

        $carrier = Auth::user()->badge;
        $this->carrier->createImages($carrier, $validator->safe()->input('images'));
        if ($carrier->details()->exists()) {
            $carrier->validate(true);
        }

        return Success(
            payload: ['carrier' => CarrierResource::make($carrier->fresh())]
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'transportation_id' => ['sometimes', 'exists:transportations,id'],
        ]);

        $carrier = Auth::user()->badge;
        if($carrier === null){
            return Error(msg: 'missing carrier');
        }
        if ($validator->safe()->exists('transportation_id')) {
            if ($carrier->details()->exists()) {
                $carrier->details()->delete();
            }

            $carrier->validate(false);
        }

        $this->carrier->update($carrier, $validator->safe()->all());

        return Success(
            msg: __('main.updated'),
            payload: [
                'carrier' => CarrierResource::make($carrier->fresh()),
            ]
        );
    }

    public function delete(Request $request): JsonResponse
    {
        $carrier = Auth::user()->badge;
        if($carrier === null){
            return Error(msg: 'missing carrier');
        }
        $this->carrier->delete($carrier);

        return Success(msg: __('main.deleted'));
    }

    public function setLocation(Request $request)
    {
        $carrier = Auth::user()->badge;
        if($carrier === null){
            return Error(msg: 'missing carrier');
        }
        $validator = Validator::make($request->all(), [
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);

        $this->carrier->setLocation($carrier, $validator->safe()->all());

        return Success(msg: __('main.Location updated'));
    }
}
