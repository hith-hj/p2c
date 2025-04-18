<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Producer;
use App\Models\V1\User;
use Illuminate\Support\Collection;

class ProducerActions
{
    use ExceptionHandler;

    public function all() 
    {
        $producers = Producer::all();
        $this->NotFound($producers->all(), __('main.producers'));

        return $producers;
    }

    public function paginate(object $request, int $perPage = 4)
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $producers = Producer::paginate($perPage);
        $this->NotFound($producers->all(), __('main.producers'));

        return $producers;
    }

    public function get(int $id) : object
    {
        $this->Required($id, __('main.user').' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->badge, __('main.Producer'));

        return $user->badge;
    }

    public function find(int $id) : object
    {
        $this->Required($id, __('main.producer').' ID');
        $producer = Producer::find($id);
        $this->NotFound($producer, __('main.producer'));

        return $producer;
    }

    public function create(object $user, array $data)
    {
        $this->Required($user, __('main.user'));
        $this->Exists($user->badge, __('main.producer'));
        $this->NotFound($data, __('main.data'));
        $producer = $user->badge()->create([
            'brand' => $data['brand'],
            'is_valid' => true,
            'rate' => 0,
        ]);
        if ($producer->branches()->count() === 0) {
            $data['phone'] = $user->phone;
        }

        $branchActions = new BranchActions();
        $created = $branchActions->create($producer, $data);
        $branchActions->setBranchAsDefault($created);

        (new LocationActions())->create($created, $data);

        return $producer;
    }

    public function update(object $producer, array $data)
    {
        $this->Required($producer, __('main.producer'));
        $this->Required($data, __('main.data'));

        return $producer->update($data);
    }

    public function delete(object $producer): bool
    {
        $this->Required($producer, __('main.Producer'));
        $producer->branches()->delete();
        $producer->orders()->delete();
        $producer->delete();

        return true;
    }
}
