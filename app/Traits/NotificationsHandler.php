<?php

declare(strict_types=1);

namespace App\Traits;

use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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
            'fcm' => $this->fcm($this->firebase_token, $title, $body, $data),
            default => $this->fcm($this->firebase_token, $title, $body, $data),
        };
    }

    public function fcm($token, $title, $body, $data)
    {
        $factory = (new FcmFactory())->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data)
            ->toToken($token);
        try {
            $res = $messaging->send($message);

            return true;
        } catch (MessagingException $e) {
            return false;
        }
    }

    private function getFCMCredentials()
    {
        return storage_path('app/fcm.json');
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
