<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Branch;
use App\Models\V1\Producer;

class BranchActions
{
    use ExceptionHandler;

    public function __construct() {}

    public function find(?int $id = null)
    {
        $this->Required($id, 'Branch ID');
        $branch = Branch::find($id);
        $this->NotFound($branch, 'Branch');

        return $branch;
    }

    public function get(?int $id = null)
    {
        $this->Required($id, 'Producer ID');
        $producer = Producer::find($id);
        $this->NotFound($producer, 'Producer');
        $this->NotFound($producer->branches, 'Branches');

        return $producer->branches;
    }

    public function create(Producer $producer, $data)
    {
        $this->Required($producer, 'Producer');
        $this->Required($data, 'Data');
        $branch = $producer->branches()->create([
            'name' => $data['name'] ?? 'Main',
            'phone' => $data['phone'],
        ]);

        (new LocationActions)->create($branch, $data);

        return $branch;
    }

    public function update($branch, $data)
    {
        return $branch->update($data);
    }

    public function delete($branch)
    {
        $this->Exists($branch->is_default, 'Is Default');
        $branch->location()->delete();

        return $branch->delete();
    }

    public function setBranchAsDefault($branch)
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
