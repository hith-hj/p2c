<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Branch;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BranchServices;
use App\Http\Resources\V1\BranchResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function __construct(public BranchServices $branch) {}

    public function all(): JsonResponse
    {
        return $this->error(msg: __('main.coming soon'));
    }

    public function get(Request $request): JsonResponse
    {
        try {
            return $this->success(payload: [
                'branches' => BranchResource::collection(
                    $this->branch->get(auth()->user()->badge?->id)
                ),
            ]);
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['sometimes', 'required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->integer('branch_id'));

            return $this->success(payload: [
                'branche' => BranchResource::make($branch),
            ]);
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:4'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'coords' => ['required', 'array', 'size:2'],
            'coords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'coords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->create(auth()->user()->badge, $validator->safe()->all());

            return $this->success(payload: [
                'branch' => BranchResource::make($branch),
            ]);
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'min:4'],
            'phone' => ['sometimes', 'required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->integer('branch_id'));
            if ($branch->producer_id !== auth()->user()->badge->id) {
                return $this->error(msg: __('main.unauthorized'), code: 403);
            }

            $this->branch->update($branch, $validator->safe()->except(['branch_id']));

            return $this->success(msg: __('main.updated'));
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->integer('branch_id'));
            if ($branch->producer_id !== auth()->user()->badge->id) {
                return $this->error(msg: __('main.unauthorized'), code: 403);
            }

            $this->branch->delete($branch);

            return $this->success(msg: __('main.deleted'));
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function setDefault(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->integer('branch_id'));
            if ($branch->producer_id !== auth()->user()->badge->id) {
                return $this->error(msg: __('main.unauthorized'), code: 403);
            }

            $this->branch->setBranchAsDefault($branch);

            return $this->success(msg: __('main.is default'));
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }
}
