<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Code;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CodesHandler
{
    public function codes(): HasMany
    {
        return $this->hasMany(Code::class, 'belongTo_id')
            ->where('belongTo_type', get_class($this));
    }

    public function code(string $type): Code
    {
        $code = $this->codes()->where('type', $type)->first();
        if ($code === null) {
            throw new \Exception("$type Code not Found");
        }

        return $code;
    }

    public function createCode(string $type, int $length = 5): static
    {
        $code = $this->generate($type, $length);
        $this->codes()->create([
            'belongTo_type' => get_class($this),
            'type' => $type,
            'code' => $code,
        ]);

        return $this;
    }

    public function deleteCode(string $type): static
    {
        if ($this->codes()->where('type', $type)->exists()) {
            $this->codes()->where('type', $type)->delete();
        }

        return $this;
    }

    private function generate(string $type, int $length): int
    {
        $codes = $this->codes;
        for ($i = 0; $i < 10; $i++) {
            $code = $this->number($length);
            if (
                mb_strlen((string) $code) === $length &&
                ! $codes->where([['type', $type], ['code', $code]])->first()
            ) {
                break;
            }
        }

        return (int) $code;
    }

    private function number(int $length): int
    {
        if ($length === null || $length <= 0) {
            throw new \Exception('Code length is not valid');
        }
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= mt_rand(0, 9);
        }

        return (int) $number;
    }
}
