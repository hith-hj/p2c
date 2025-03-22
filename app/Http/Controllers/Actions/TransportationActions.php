<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Transportation;

class TransportationActions
{
    use ExceptionHandler;

    public function all($request, $perPage = 4)
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }
        $transportaions = Transportation::paginate($perPage);
        $this->NotFound($transportaions->all(), 'Transportations');

        return $transportaions;
    }

    public function find(?int $id = null)
    {
        $this->Required($id, 'Carrier ID');
        $transportaion = Transportation::find($id);
        $this->NotFound($transportaion, 'Transportation');

        return $transportaion;
    }

    public function create($user, $data)
    {
        $this->Required($user, 'User');
        $this->Exists($user->badge, 'Carrier');
        $this->NotFound($data, 'Carrier data');
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

    public function update($transportaion, $data)
    {
        $this->Required($transportaion, 'Carrier');
        $this->Required($data, 'data');

        return $transportaion->update($data);
    }

    public function delete($transportaion)
    {
        $this->Required($transportaion, 'Carrier');
        $transportaion->delete();

        return true;
    }
}
