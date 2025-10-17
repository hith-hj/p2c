<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Carrier;
use App\Models\V1\CarrierDetails;
use App\Models\V1\Location;
use App\Models\V1\User;
use Illuminate\Foundation\Auth\User as Auth;
use Illuminate\Support\Collection;

final class CarrierServices
{
    public function all(): Collection
    {
        $carriers = Carrier::all();
        NotFound($carriers, 'carrier');

        return $carriers->load([
            'transportation',
            'details',
            'images',
        ]);
    }

    public function paginate(object $request, int $perPage = 4): object
    {
        if ($request->filled('perPage')) {
            $perPage = $request->perPage;
        }

        $carriers = Carrier::paginate($perPage);
        NotFound($carriers->all(), 'carrier');

        return $carriers;
    }

    public function get(int $id): object
    {
        $user = User::find($id);
        NotFound($user, 'user');
        NotFound($user->badge, 'carrier');

        return $user->badge->load([
            'orders',
            'transportation',
            'fees',
            'details',
            'location',
            'images',
        ]);
    }

    public function find(int $id): Carrier
    {
        $carrier = Carrier::find($id);
        NotFound($carrier, 'carrier');

        return $carrier;
    }

    public function create(Auth $user, array $data): Carrier
    {
        Exists($user->badge, 'carrier');
        NotFound($data, 'data');

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

    public function createDetails(Carrier $carrier, array $data): CarrierDetails
    {
        Exists($carrier->details, 'carrier details');
        Required($data, 'details');

        return $carrier->details()->create([
            'plate_number' => $data['plate_number'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'],
            'color' => $data['color'],
        ]);
    }

    public function createImages(Carrier $carrier, array $data): bool
    {
        Truthy($carrier->images()->count() > 0, 'images exists');
        Required($data, 'images');
        $carrier->multibleImage($data);

        return true;
    }

    public function update(Carrier $carrier, array $data): bool
    {
        Required($data, 'data');

        return $carrier->update($data);
    }

    public function delete(Carrier $carrier): bool
    {
        $carrier->details()->delete();
        $carrier->images()->delete();
        $carrier->delete();

        return true;
    }

    public function setLocation(Carrier $carrier, array $data): bool|Location
    {
        Required($data, 'data');

        return (new LocationServices())->edit($carrier, $data);
    }
}
