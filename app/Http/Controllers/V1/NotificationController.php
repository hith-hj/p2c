<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificationResource;
use App\Http\Services\NotificationServices;
use App\Http\Validators\NotificationValidators;
use Error;
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

    public function view(Request $request): JsonResponse
    {
        $validator = NotificationValidators::view($request->all());

        $this->noti->view($validator->safe()->array('notifications'));

        return Success();
    }

    public function delete(Request $request)
    {
        $validator = NotificationValidators::delete($request->all());

        $notification = $this->noti->find($validator->safe()->integer('notification_id'));
        if (
            $notification->belongTo_id === Auth::id() &&
            $notification->belongTo_type === Auth::user()::class
        ) {
            $this->noti->delete($notification);
            return Success();
        }
        return Error(msg: __('main.un authorized'), code: 403);
    }

    public function clear()
    {
        $this->noti->clear(Auth::user());
        if(Auth::user()->badge !== null){
            $this->noti->clear(Auth::user()->badge);
        }
        return Success();
    }

}
