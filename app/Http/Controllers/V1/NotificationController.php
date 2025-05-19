<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificationResource;
use App\Http\Services\NotificationServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function __construct(public NotificationServices $noti) {}

    public function all(Request $request): JsonResponse
    {
        $notis = $this->noti->all(Auth::user());

        return Success(payload: ['notifications' => NotificationResource::collection($notis)]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => ['required', 'exists:notifications,id'],
        ]);
        $noti = $this->noti->find($validator->safe()->integer('notification_id'));

        return Success(payload: ['notification' => $noti]);
    }

    public function viewed(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => ['required', 'exists:notifications,id'],
        ]);
        $this->noti->viewed($validator->safe()->integer('notification_id'));

        return Success();
    }

    public function multipleViewed(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notifications' => ['required', 'array', 'min:1'],
            'notifications.*' => ['required', 'exists:notifications,id'],
        ]);
        $this->noti->multipleViewed($validator->safe()->array('notifications'));

        return Success();
    }
}
