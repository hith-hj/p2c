<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewServices;
use App\Http\Validators\ReviewValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(public ReviewServices $review) {}

    public function all(): JsonResponse
    {
        return Success(payload: ['reviews' => $this->review->all(Auth::user()->badge)]);
    }

    public function create(Request $request)
    {
        $validator = ReviewValidators::create($request->all());

        $review = $this->review->create(Auth::user()->badge, $validator->safe()->all());

        return Success(msg: 'review created', payload: ['review' => $review]);
    }
}
