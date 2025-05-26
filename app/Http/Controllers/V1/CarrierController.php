<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarrierResource;
use App\Http\Services\CarrierServices;
use App\Http\Validators\CarrierValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validator = CarrierValidators::find($request->all());

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
        $validator = CarrierValidators::create($request->all());

        $carrier = $this->carrier->create(Auth::user(), $validator->safe()->all());

        return Success(payload: ['carrier' => CarrierResource::make($carrier)]);
    }

    public function createDetails(Request $request): JsonResponse
    {
        $carrier = Auth::user()->badge;
        if ($carrier === null) {
            return Error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = CarrierValidators::createDetails($request->all());

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
        $carrier = Auth::user()->badge;
        if ($carrier === null) {
            return Error(msg: __('main.carrier').' '.__('main.not found'));
        }

        $validator = CarrierValidators::createImages($request->all());

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
        $validator = CarrierValidators::update($request->all());

        $carrier = Auth::user()->badge;
        if ($carrier === null) {
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
        if ($carrier === null) {
            return Error(msg: 'missing carrier');
        }

        $this->carrier->delete($carrier);

        return Success(msg: __('main.deleted'));
    }

    public function setLocation(Request $request)
    {
        $carrier = Auth::user()->badge;
        if ($carrier === null) {
            return Error(msg: 'missing carrier');
        }

        $validator = CarrierValidators::setLocation($request->all());

        $this->carrier->setLocation($carrier, $validator->safe()->all());

        return Success(msg: __('main.Location updated'));
    }
}
