<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\ExceptionHandler;
use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Illuminate\Support\Collection;

class BranchServices
{
    use ExceptionHandler;

    public function find(int $id): Branch
    {
        $branch = Branch::find($id);
        $this->NotFound($branch, 'branch');

        return $branch;
    }

    public function get(int $id): Collection
    {
        $producer = Producer::find($id);
        $this->NotFound($producer, 'producer');
        $this->NotFound($producer->branches, 'branches');

        return $producer->branches;
    }

    public function create(Producer $producer, array $data): Branch
    {
        $this->Required($data, 'data');
        $branch = $producer->branches()->create([
            'name' => $data['name'] ?? 'Main',
            'phone' => $data['phone'],
        ]);

        (new LocationServices())->create($branch, $data);

        return $branch;
    }

    public function update(Branch $branch, array $data): bool
    {
        $this->Required($data, 'data');

        return $branch->update($data);
    }

    public function delete(Branch $branch): bool
    {
        $this->Truthy($branch->is_default, 'is default');
        $branch->location()->delete();

        return $branch->delete();
    }

    public function setBranchAsDefault(Branch $branch): bool
    {
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
