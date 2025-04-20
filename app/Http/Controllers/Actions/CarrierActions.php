<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\DocumentHandler;
use App\ExceptionHandler;
use App\Models\V1\Carrier;
use App\Models\V1\User;

class CarrierActions
{
    use DocumentHandler;
    use ExceptionHandler;

    public function all()
    {
        $carriers = Carrier::all();
        $this->NotFound($carriers, __('main.carrier'));

        return $carriers;
    }

    public function paginate(object $request, int $perPage = 4)
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $carriers = Carrier::paginate($perPage);
        $this->NotFound($carriers->all(), __('main.carrier'));

        return $carriers;
    }

    public function get(int $id)
    {
        $this->Required($id, __('main.user').' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->badge, __('main.carrier'));

        return $user->badge;
    }

    public function find(int $id)
    {
        $this->Required($id, __('main.carrier').' ID');
        $carrier = Carrier::find($id);
        $this->NotFound($carrier, __('main.carrier'));

        return $carrier;
    }

    public function create(object $user, array $data)
    {
        $this->Required($user, __('main.user'));
        $this->Exists($user->badge, __('main.carrier'));
        $this->NotFound($data, __('main.data'));

        return $user->badge()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'transportation_id' => $data['transportation_id'],
            'is_valid' => false,
            'is_online' => false,
            'is_available' => false,
            'rate' => 0,
        ]);
    }

    public function createDetails(object $carrier, array $data)
    {
        $this->Required($carrier, __('main.carrier'));
        $this->Exists($carrier->details, __('main.carrier').' '.__('main.details'));
        $this->Required($data, __('main.details'));

        return $carrier->details()->create([
            'plate_number' => $data['plate_number'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'],
            'color' => $data['color'],
        ]);
    }

    public function createDocuments(object $carrier, array $data): bool
    {
        $this->Required($carrier, __('main.carrier'));
        $this->Exists($carrier->documents()->count() > 0, __('main.carrier').' '.__('main.documents'));
        $this->Required($data, __('main.documents'));
        $this->multible($data, $carrier->id, get_class($carrier));

        return true;
    }

    public function update(object $carrier, array $data)
    {
        $this->Required($carrier, __('main.carrier'));
        $this->Required($data, __('main.data'));

        return $carrier->update($data);
    }

    public function delete(object $carrier): bool
    {
        $this->Required($carrier, __('main.carrier'));
        $carrier->details()->delete();
        $carrier->documents()->delete();
        $carrier->profileImage()->delete();
        $carrier->delete();

        return true;
    }
}
