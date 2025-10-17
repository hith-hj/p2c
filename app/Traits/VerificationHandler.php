<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\CodesTypes;
use App\Enums\NotificationTypes;

trait VerificationHandler
{
    public function verify($by = 'fcm'): static
    {
        $this->checkFields();
        $this->checkMethods();
        $this->createCode(type: CodesTypes::verification->name, timeToExpire: '45:m')
            ->update([
                'verified_at' => null,
                'verified_by' => $by,
            ]);

        $code = $this->code(CodesTypes::verification->name);
        $this->notify(
            title: 'verification code',
            body: "Your code: $code->code, expire at {$code->expire_at->diffForHumans()}",
            data: ['type' => NotificationTypes::verification->value, 'code' => $code],
            provider: $by
        );

        return $this;
    }

    public function verified(): static
    {
        $this->checkFields();
        $this->checkMethods();
        $this->deleteCode(CodesTypes::verification->name)->touch('verified_at');

        return $this;
    }

    private function checkFields()
    {
        if (count(array_diff(['verified_at', 'verified_by'], array_keys($this->toArray()))) !== 0) {
            throw new \Exception(class_basename($this::class).' missing verification fields');
        }
    }

    private function checkMethods()
    {
        if (! method_exists($this, 'code')) {
            throw new \Exception(class_basename($this::class).' missing codes Handler');
        }

        if (! method_exists($this, 'notify')) {
            throw new \Exception(class_basename($this::class).' missing notifications Handler');
        }
    }
}
