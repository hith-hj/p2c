<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Review;
use App\Traits\ExceptionHandler;
use Illuminate\Support\Collection;

class ReviewServices
{
	use ExceptionHandler;

    private $allowedModels = ['User', 'Carrier', 'Producer', 'Customer'];

	public function all(object $object)
	{
		$this->Truthy(!method_exists($object, 'reviews'), 'missing');
	}

	public function create(object $object, array $data)
	{
		$this->Truthy(!method_exists($object, 'reviews'), 'missing');
		$this->checkAndCastData($data,[
			'content'=>'string',
			'rate'=>'int',
		]);
		return $object->createReview($data);
	}


	private function checkAndCastData(array $data = [], $requiredFields = []): array
    {
        $this->Truthy(empty($data), 'data is empty');
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
