<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Illuminate\Support\Collection;

final class BranchServices
{
    public function find(int $id): Branch
    {
        $branch = Branch::find($id);
        NotFound($branch, 'branch');

        return $branch;
    }

    public function get(int $id): Collection
    {
        $producer = Producer::find($id);
        NotFound($producer, 'producer');
        NotFound($producer->branches, 'branches');

        return $producer->branches->load(['location']);
    }

    public function create(Producer $producer, array $data): Branch
    {
        Required($data, 'data');
        $branch = $producer->branches()->create([
            'name' => $data['name'] ?? 'Main',
            'phone' => $data['phone'],
        ]);

        (new LocationServices())->create($branch, $data);

        return $branch;
    }

    public function update(Branch $branch, array $data): bool
    {
        Required($data, 'data');

        return $branch->update($data);
    }

    public function delete(Branch $branch): bool
    {
        Truthy($branch->is_default, 'is default');
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
