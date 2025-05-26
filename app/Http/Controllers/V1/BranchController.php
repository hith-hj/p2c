<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BranchResource;
use App\Http\Services\BranchServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function __construct(public BranchServices $branch) {}

    public function all(): JsonResponse
    {
        return Error(msg: __('main.coming soon'));
    }

    public function get(Request $request): JsonResponse
    {
        return Success(payload: [
            'branches' => BranchResource::collection(
                $this->branch->get(Auth::user()->badge?->id),
            ),
        ]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['sometimes', 'required', 'exists:branches,id'],
        ]);

        $branch = $this->branch->find($validator->safe()->integer('branch_id'));

        return Success(payload: [
            'branche' => BranchResource::make($branch),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);

        $branch = $this->branch->create(Auth::user()->badge, $validator->safe()->all());

        return Success(payload: [
            'branch' => BranchResource::make($branch),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'min:4'],
            'phone' => ['sometimes', 'required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
        ]);

        $branch = $this->branch->find($validator->safe()->integer('branch_id'));
        if ($branch->producer_id !== Auth::user()->badge->id) {
            return Error(msg: __('main.unauthorized'), code: 403);
        }

        $this->branch->update($branch, $validator->safe()->except(['branch_id']));

        return Success(msg: __('main.updated'));
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $branch = $this->branch->find($validator->safe()->integer('branch_id'));
        if ($branch->producer_id !== Auth::user()->badge->id) {
            return Error(msg: __('main.unauthorized'), code: 403);
        }

        $this->branch->delete($branch);

        return Success(msg: __('main.deleted'));
    }

    public function setDefault(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $branch = $this->branch->find($validator->safe()->integer('branch_id'));
        if ($branch->producer_id !== Auth::user()->badge->id) {
            return Error(msg: __('main.unauthorized'), code: 403);
        }

        $this->branch->setBranchAsDefault($branch);

        return Success(msg: __('main.is default'));
    }
}
