<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function __construct(public ReviewServices $review) {}

    public function all(): JsonResponse
    {
        return Success(payload: ['reviews' => $this->review->all(Auth::user()->badge)]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'type' => ['required', 'string'],
            'content' => ['nullable', 'string', 'max:700'],
            'rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        $review = $this->review->create(Auth::user()->badge, $validator->safe()->all());

        return Success(msg: 'review created', payload: ['review' => $review]);
    }
}
