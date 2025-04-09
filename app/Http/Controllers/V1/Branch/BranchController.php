<?php

namespace App\Http\Controllers\V1\Branch;

use App\Http\Controllers\Actions\BranchActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BranchResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function __construct(public BranchActions $branch) {}

    public function all()
    {
        return $this->error(msg: 'Not done yet');
    }

    public function get(Request $request)
    {
        try {
            return $this->success(payload: [
                'branches' => BranchResource::collection(
                    $this->branch->get(auth()->user()->badge?->id)
                ),
            ]);
        } catch (\Throwable $e) {
            $code = $e->getCode() === 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $code);
        }
    }

    public function find(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['sometimes', 'required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->input('branch_id'));

            return $this->success(payload: [
                'branche' => BranchResource::make($branch),
            ]);
        } catch (\Throwable $e) {
            $code = $e->getCode() === 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $code);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:4'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'coords' => ['required', 'array', 'size:2'],
            'coords.long' => ['required', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'coords.lat' => ['required', 'numeric', 'between:-90,90', 'required_with:longitude'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->create(auth()->user()->badge, $validator->safe()->all());

            return $this->success(payload: [
                'branch' => BranchResource::make($branch),
            ]);
        } catch (\Throwable $e) {
            $code = $e->getCode() === 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $e->getCode());
        }
    }

    public function update(Request $request)
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
            $branch = $this->branch->find($validator->safe()->input('branch_id'));
            if ($branch->producer_id != auth()->user()->badge->id) {
                return $this->error(msg: 'Un Authorized', code: 403);
            }
            $this->branch->update($branch, $validator->safe()->except(['branch_id']));

            return $this->success(msg: 'Branch Updated');
        } catch (\Throwable $e) {
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $code);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->input('branch_id'));
            if ($branch->producer_id != auth()->user()->badge->id) {
                return $this->error(msg: 'Un Authorized', code: 403);
            }
            $this->branch->delete($branch);

            return $this->success(msg: 'Branch Deleted');
        } catch (\Throwable $e) {
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $code);
        }
    }

    public function setDefault(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        try {
            $branch = $this->branch->find($validator->safe()->input('branch_id'));
            if ($branch->producer_id != auth()->user()->badge->id) {
                return $this->error(msg: 'Un Authorized', code: 403);
            }
            $this->branch->setBranchAsDefault($branch);

            return $this->success(msg: 'Branch is default');
        } catch (\Throwable $e) {
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return $this->error(msg: $e->getMessage(), code: $code);
        }
    }
}
