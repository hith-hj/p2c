<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use App\ExceptionHandler;
use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Exception;
use Illuminate\Support\Collection;

class BranchServices
{
    use ExceptionHandler;

    public function find(int $id): Branch|Exception
    {
        $this->Required($id, __('main.branch').' ID');
        $branch = Branch::where('id', $id)->first();
        $this->NotFound($branch, __('main.branch'));

        return $branch;
    }

    public function get(int $id): Collection|Exception
    {
        $this->Required($id, __('main.producer').' ID');
        $producer = Producer::find($id);
        $this->NotFound($producer, __('main.producer'));
        $this->NotFound($producer->branches, __('main.branches'));

        return $producer->branches;
    }

    public function create(Producer $producer, array $data): Branch|Exception
    {
        $this->Required($producer, __('main.producer'));
        $this->Required($data, __('main.data'));
        $branch = $producer->branches()->create([
            'name' => $data['name'] ?? 'Main',
            'phone' => $data['phone'],
        ]);

        (new LocationServices())->create($branch, $data);

        return $branch;
    }

    public function update(Branch $branch, array $data): bool|Exception
    {
        $this->Required($branch, __('main.branch'));
        $this->Required($data, __('main.data'));
        return $branch->update($data);
    }

    public function delete(Branch $branch): bool|Exception
    {
        $this->Exists($branch->is_default, __('main.is default'));
        $branch->location()->delete();

        return $branch->delete();
    }

    public function setBranchAsDefault(Branch $branch): bool|Exception
    {
        $this->Required($branch,__('main.branch'));
        $default = Branch::where([
            ['producer_id', $branch->producer_id],
            ['is_default', true],
        ])->first();
        if ($default) {
            $default->update(['is_default' => false]);
        }

        $branch->update(['is_default' => true]);

        return true;
    }
}
