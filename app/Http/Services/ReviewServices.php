<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Review;
use App\Traits\ExceptionHandler;
use Illuminate\Support\Collection;

class ReviewServices
{
    use ExceptionHandler;

    public function all(object $object): Collection
    {
        $this->Truthy(! method_exists($object, 'reviews'), 'missing');
        $reviews = $object->reviews;
        $this->NotFound($reviews, 'reviews');

        return $reviews;
    }

    public function create(object $reviewer, array $data): Review
    {
        $this->Required($reviewer, 'reviewer');
        $this->Required($data, 'data');
        $this->checkAndCastData($data, [
            'content' => 'string',
            'rate' => 'int',
        ]);
        $model = $this->getPreparedModel($data);
        $this->Truthy(
            $model::class === $reviewer::class && $model->id === $reviewer->id,
            'You cant review this'
        );
        $query = Review::where([
            ['belongTo_id', $model->id],
            ['belongTo_type', $model::class],
            ['reviewer_id', $reviewer->id],
            ['reviewer_type', $reviewer::class],
        ]);
        $this->Truthy(
            ($query->exists() && date_diff(now(), $query->first()->created_at)->d < 30),
            'review exists'
        );

        $review = $model->createReview($reviewer, $data);
        $model->updateRate();

        return $review;
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

    private function getPreparedModel($data)
    {
        $id = $data['id'];
        $type = ucfirst($data['type']);
        $class = "App\\Models\\V1\\$type";
        $this->Truthy(! class_exists($class), 'invalid class type');
        $model = $class::find($id);
        $this->NotFound($model, '$type id $id ');
        $this->Truthy(! method_exists($model, 'reviews'), 'model missing reviews()');

        return $model;
    }
}
