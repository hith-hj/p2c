<?php

namespace App\Http\Controllers\Actions;

use App\DocumentHandler;
use App\ExceptionHandler;
use App\Models\V1\Carrier;
use App\Models\V1\User;

class CarrierActions
{
    use DocumentHandler, ExceptionHandler;

    public function all($request, $perPage = 4)
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }
        $carriers = Carrier::paginate($perPage);
        $this->NotFound($carriers->all(), 'Carriers');

        return $carriers;
    }

    public function get(?int $id = null)
    {
        $this->Required($id, 'User ID');
        $user = User::find($id);
        $this->NotFound($user, 'User');
        $this->NotFound($user->badge, 'Carrier');

        return $user->badge;
    }

    public function find(?int $id = null)
    {
        $this->Required($id, 'Carrier ID');
        $carrier = Carrier::find($id);
        $this->NotFound($carrier, 'Carrier');

        return $carrier;
    }

    public function create($user, $data)
    {
        $this->Required($user, 'User');
        $this->Exists($user->badge, 'Carrier');
        $this->NotFound($data, 'Carrier data');
        $carrier = $user->badge()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'transportation_id' => $data['transportaion_id'],
            'is_valid' => false,
            'is_online' => false,
            'is_available' => false,
            'rate' => 0,
        ]);

        return $carrier;
    }

    public function createDetails($carrier, $data)
    {
        $this->Required($carrier, 'Carrier');
        $this->Exists($carrier->details, 'Carrier Details');
        $this->Required($data, 'Carrier Deatils');

        return $carrier->details()->create([
            'plate_number' => $data['plate_number'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'],
            'color' => $data['color'],
        ]);
    }

    public function createDocuments($carrier, $data)
    {
        $this->Required($carrier, 'Carrier');
        $this->Exists($carrier->documents()->count() > 0, 'Carrier Documents');
        $this->Required($data, 'Carrier Documents');
        $res = $this->multible($data, $carrier->id, class_basename($carrier));

        return true;
    }

    public function update($carrier, $data)
    {
        $this->Required($carrier, 'Carrier');
        $this->Required($data, 'data');

        return $carrier->update($data);
    }

    public function delete($carrier)
    {
        $this->Required($carrier, 'Carrier');
        $carrier->details()->delete();
        $carrier->documents()->delete();
        $carrier->profileImage()->delete();
        $carrier->delete();

        return true;
    }
}
