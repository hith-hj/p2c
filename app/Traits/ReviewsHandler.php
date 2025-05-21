<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Review;

trait ReviewsHandler
{
    public function reviews()
    {
        return $this->hasMany(Review::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function createReview(object $reviewer, array $data)
    {
        throw_if(empty($data), 'missing review data');

        return $this->reviews()->create([
            'reviewer_id' => $reviewer->id,
            'reviewer_type' => $reviewer::class,
            'content' => $data['content'],
            'rate' => $data['rate'],
        ]);
    }

    public function updateRate()
    {
        throw_if(! isset($this->rate), 'rate is missing');
        $sum = $this->reviews()->sum('rate');
        $count = $this->reviews()->count();
        $rate = round(($sum / $count) / 2, 1);

        return $this->update(['rate' => $rate]);
    }
}
