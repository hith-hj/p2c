<?php

declare(strict_types=1);

namespace App\Traits;

use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

trait NotificationsHandler
{
    public function notifyPhone(
        string $title = '',
        string $body = '',
        array $data = [],
        string $provider = 'fcm'
    ) {
        if (app()->environment('testing')) {
            return true;
        }

        return match ($provider) {
            'sms' => $this->sms(),
            'fcm' => $this->fcm($title, $body, $data),
            default => $this->fcm($title, $body, $data),
        };
    }

    public function fcm($title, $body, $data)
    {
        if ($this->firebase_token === null) {
            return true;
        }
        $token = $this->firebase_token;
        $factory = (new FcmFactory())->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $data = [
            'title' => $title,
            'body' => $body,
        ];
        $message = CloudMessage::new()
            ->withNotification($data)
            ->withAndroidConfig($this->getFCMAndroidConfig())
            ->toToken($token);
        if (! empty($data)) {
            $message->withData($data);
        }
        try {
            $res = $messaging->send($message);
            $this->store([...$data, 'result' => $res]);

            return true;
        } catch (MessagingException $e) {
            return false;
        }
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

    public function store(array $data)
    {
        if (! method_exists($this, 'notifications')) {
            return;
        }

        return $this->notifications()->create([
            'belongTo_type' => $this::class,
            'title' => $data['title'],
            'payload' => serialize($data),
            'status' => 0,
        ]);
    }

    private function sms()
    {
        return true;
    }

    public function notifyEmail()
    {
        return true;
    }
}
