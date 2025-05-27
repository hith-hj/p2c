<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificationResource;
use App\Http\Services\NotificationServices;
use App\Http\Validators\NotificationValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validator = NotificationValidators::find($request->all());

        $noti = $this->noti->find($validator->safe()->integer('notification_id'));

        return Success(payload: ['notification' => $noti]);
    }

    public function viewed(Request $request): JsonResponse
    {
        $validator = NotificationValidators::viewed($request->all());

        $this->noti->viewed($validator->safe()->integer('notification_id'));

        return Success();
    }

    public function multipleViewed(Request $request): JsonResponse
    {
        $validator = NotificationValidators::multibleViewed($request->all());

        $this->noti->multipleViewed($validator->safe()->array('notifications'));

        return Success();
    }

    public function delete(Request $request)
    {
        $validator = NotificationValidators::delete($request->all());

        $this->noti->delete($validator->safe()->integer('notification_id'));
        return Success(msg:'Notification deleted');
    }

    public function multipleDelete(Request $request)
    {
        $validator = NotificationValidators::multibleDelete($request->all());

        $this->noti->multipleDelete($validator->safe()->array('notifications'));
        return Success(msg:'Notifications deleted');
    }
}
