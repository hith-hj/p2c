<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Transportation;

class TransportationActions
{
    use ExceptionHandler;

    public function all()
    {
        $transportaions = Transportation::all();
        $this->NotFound($transportaions, __('main.transportations'));

        return $transportaions;
    }

    public function paginate(object $request, int $perPage = 4)
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $transportaions = Transportation::paginate($perPage);
        $this->NotFound($transportaions->all(), __('main.transportations'));

        return $transportaions;
    }

    public function find(int $id)
    {
        $this->Required($id, __('main.carrier').' ID');
        $transportaion = Transportation::find($id);
        $this->NotFound($transportaion, __('main.transportation'));

        return $transportaion;
    }

    public function create(object $user, array $data)
    {
        $this->Required($user, __('main.user'));
        $this->Exists($user->badge, __('main.carrier'));
        $this->NotFound($data, __('main.data'));
        $transportaion = $user->badge()->create([
            'brand' => $data['brand'],
            'is_valid' => true,
            'rate' => 0,
        ]);
        if ($transportaion->branches()->count() === 0) {
            $data['phone'] = $user->phone;
        }

        return $transportaion;
    }

    public function update(object $transportaion, array $data)
    {
        $this->Required($transportaion, __('main.carrier'));
        $this->Required($data, __('main.data'));

        return $transportaion->update($data);
    }

    public function delete(object $transportaion): bool
    {
        $this->Required($transportaion, __('main.carrier'));
        $transportaion->delete();

        return true;
    }

    public function getMatchedTransportation(int $weight)
    {
        $maxCapacity = Transportation::max('capacity');

        if ($weight > $maxCapacity) {
            throw new \Exception('Max Capacity Currently is '.$maxCapacity);
        }

        return Transportation::orderBy('capacity')
            ->where('capacity', '>=', $weight)
            ->first();

    }
}
