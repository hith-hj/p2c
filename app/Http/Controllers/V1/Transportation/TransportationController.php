<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Transportation;

use App\Http\Controllers\Actions\TransportationActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TransportationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportationController extends Controller
{
    public function __construct(private readonly TransportationActions $trans) {}

    public function all(Request $request)
    {
        try {
            return $this->success(payload: [
                'Transportaions' => TransportationResource::collection($this->trans->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            return $this->success(payload: [
                'Transportaions' => TransportationResource::make($this->trans->find($validator->safe()->input('transportation_id'))),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
