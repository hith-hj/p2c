<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Notification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

trait NotificationsHandler
{
    private string $title = '';

    private string $body = '';

    private array $data = [];

    public function notify(
        string $title = '',
        string $body = '',
        array $data = [],
        string $provider = 'fcm'
    ) {
        if (App::environment('testing')) {
            return true;
        }
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;

        return match ($provider) {
            'fcm' => $this->fcm(),
            'sms' => $this->sms(),
            'email' => $this->email(),
            default => $this->fcm(),
        };
    }

    public function fcm()
    {
        if ($this->firebase_token === null) {
            return true;
        }
        $token = $this->firebase_token;
        $factory = (new FcmFactory())->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $notification = ['title' => $this->title, 'body' => $this->body];
        $message = CloudMessage::new()->toToken($token)
            ->withNotification(FcmNotification::fromArray($notification))
            ->withAndroidConfig($this->getFCMAndroidConfig())
            ->withData(MessageData::fromArray($this->data));

        try {
            $res = $messaging->send($message);
            $this->store(['result' => $res]);

            return true;
        } catch (MessagingException $e) {
            return false;
        }
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    private function store(array $extra)
    {
        if (! method_exists($this, 'notifications')) {
            return;
        }

        return $this->notifications()->create([
            'title' => $this->title,
            'payload' => serialize(['body' => $this->body, 'data' => $this->data, 'extra' => $extra]),
            'status' => 0,
        ]);
    }

    private function sms()
    {
        return true;
    }

    private function email()
    {
        return true;
    }

    private function getFCMCredentials()
    {
        return storage_path('app/fcm.json');
    }

    private function getFCMAndroidConfig()
    {
        return AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'high',
            'notification' => [
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
                'sound' => 'default',
            ],
        ]);
    }
}
