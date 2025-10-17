<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Producer;
use App\Models\V1\User;
use Illuminate\Foundation\Auth\User as Auth;
use Illuminate\Support\Collection;

final class ProducerServices
{
    public function all(): Collection
    {
        $producers = Producer::all();
        NotFound($producers, 'producers');

        return $producers;
    }

    public function paginate(object $request, int $perPage = 4): object
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $producers = Producer::paginate($perPage);
        NotFound($producers->all(), 'producers');

        return $producers;
    }

    public function get(int $id): Producer
    {
        $user = User::find($id);
        NotFound($user, 'user');
        NotFound($user->badge, 'Producer');

        return $user->badge;
    }

    public function find(int $id): Producer
    {
        $producer = Producer::find($id);
        NotFound($producer, 'producer');

        return $producer;
    }

    public function create(Auth $user, array $data): Producer
    {
        Exists($user->badge, 'producer');
        NotFound($data, 'data');
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
        Required($data, 'data');

        return $producer->update($data);
    }

    public function delete(Producer $producer): bool
    {
        $producer->branches()->delete();
        $producer->delete();

        return true;
    }
}
