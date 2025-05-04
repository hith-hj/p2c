<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\ExceptionHandler;
use App\Models\V1\Producer;
use App\Models\V1\User;
use Illuminate\Foundation\Auth\User as Auth;
use Illuminate\Support\Collection;

class ProducerServices
{
    use ExceptionHandler;

    public function all(): Collection
    {
        $producers = Producer::all();
        $this->NotFound($producers, __('main.producers'));

        return $producers;
    }

    public function paginate(object $request, int $perPage = 4): object
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $producers = Producer::paginate($perPage);
        $this->NotFound($producers->all(), __('main.producers'));

        return $producers;
    }

    public function get(int $id): Producer
    {
        $this->Required($id, __('main.user').' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->badge, __('main.Producer'));

        return $user->badge;
    }

    public function find(int $id): Producer
    {
        $this->Required($id, __('main.producer').' ID');
        $producer = Producer::where('id', $id)->first();
        $this->NotFound($producer, __('main.producer'));

        return $producer;
    }

    public function create(Auth $user, array $data): Producer
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

        $branchServices = new BranchServices();
        $created = $branchServices->create($producer, $data);
        $branchServices->setBranchAsDefault($created);

        return $producer;
    }

    public function update(Producer $producer, array $data): bool
    {
        $this->Required($producer, __('main.producer'));
        $this->Required($data, __('main.data'));

        return $producer->update($data);
    }

    public function delete(Producer $producer): bool
    {
        $this->Required($producer, __('main.Producer'));
        $producer->branches()->delete();
        // $producer->orders()->delete();
        $producer->delete();

        return true;
    }
}
