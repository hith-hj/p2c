<?php

namespace App\Traits;

use App\Models\V1\Review;

trait ReviewsHandler
{
    public function reviews(){
        return $this->hasMany(Review::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function createReview(array $data)
    {
        throw_if(empty($data),'missing review data');
        return $this->reviews()->create([
            'reviewer_id'=>$this->id,
            'reviewer_type'=>$this::class,
            'content'=>$data['content'],
            'rate'=>$data['rate'],
        ]);
    }
}
