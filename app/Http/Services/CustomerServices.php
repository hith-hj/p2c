<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Enums\CustomerStatus;
use App\Models\V1\Customer;
use App\Traits\ExceptionHandler;
use Illuminate\Support\Facades\Hash;

final class CustomerServices
{
    use ExceptionHandler;

    public function createIfNotExists(array $data): Customer
    {
        $data = $this->checkAndCastData($data, [
            'phone' => 'string',
            'name' => 'string',
            'cords' => 'array',
        ]);

        $customer = Customer::where('phone', $data['phone'])->first();
        if ($customer) {
            return $customer;
        }

        return $this->create($data);
    }

    public function find(int $id): Customer
    {
        $customer = Customer::find($id);
        $this->Truthy($customer === null, 'Not Found');

        return $customer;
    }

    public function create(array $data): Customer
    {
        $data = $this->checkAndCastData($data, [
            'phone' => 'string',
            'name' => 'string',
            'cords' => 'array',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['phone']),
            'status' => CustomerStatus::fresh->value,
        ]);
        (new LocationServices())->create($customer, $data);

        return $customer;
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        $this->Truthy(empty($data), 'data is required');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = array_diff(array_keys($requiredFields), array_keys($data));
        $this->Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));
        foreach ($requiredFields as $key => $value) {
            settype($data[$key], $value);
        }

        return $data;
    }
}
