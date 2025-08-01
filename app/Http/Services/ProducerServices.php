<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Producer;
use App\Models\V1\User;
use App\Traits\ExceptionHandler;
use Illuminate\Foundation\Auth\User as Auth;
use Illuminate\Support\Collection;

final class ProducerServices
{
    use ExceptionHandler;

    public function all(): Collection
    {
        $producers = Producer::all();
        $this->NotFound($producers, 'producers');

        return $producers;
    }

    public function paginate(object $request, int $perPage = 4): object
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $producers = Producer::paginate($perPage);
        $this->NotFound($producers->all(), 'producers');

        return $producers;
    }

    public function get(int $id): Producer
    {
        $user = User::find($id);
        $this->NotFound($user, 'user');
        $this->NotFound($user->badge, 'Producer');

        return $user->badge;
    }

    public function find(int $id): Producer
    {
        $producer = Producer::find($id);
        $this->NotFound($producer, 'producer');

        return $producer;
    }

    public function create(Auth $user, array $data): Producer
    {
        $this->Exists($user->badge, 'producer');
        $this->NotFound($data, 'data');
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
        $this->Required($data, 'data');

        return $producer->update($data);
    }

    public function delete(Producer $producer): bool
    {
        $producer->branches()->delete();
        $producer->delete();

        return true;
    }
}
