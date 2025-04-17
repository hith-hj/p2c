<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Branch;
use App\Models\V1\Producer;

class BranchActions
{
    use ExceptionHandler;

    public function find($id)
    {
        $this->Required($id, __('main.branch').' ID');
        $branch = Branch::find($id);
        $this->NotFound($branch, __('main.branch'));

        return $branch;
    }

    public function get($id)
    {
        $this->Required($id, __('main.producer').' ID');
        $producer = Producer::find($id);
        $this->NotFound($producer, __('main.producer'));
        $this->NotFound($producer->branches, __('main.branches'));

        return $producer->branches;
    }

    public function create(object $producer, array $data)
    {
        $this->Required($producer, __('main.producer'));
        $this->Required($data, __('main.data'));
        $branch = $producer->branches()->create([
            'name' => $data['name'] ?? 'Main',
            'phone' => $data['phone'],
        ]);

        (new LocationActions())->create($branch, $data);

        return $branch;
    }

    public function update(object $branch, array $data)
    {
        return $branch->update($data);
    }

    public function delete(object $branch)
    {
        $this->Exists($branch->is_default, __('main.is default'));
        $branch->location()->delete();

        return $branch->delete();
    }

    public function setBranchAsDefault(object $branch): bool
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
