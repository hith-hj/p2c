<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Code;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

trait CodesHandler
{
    public function codes(): HasMany
    {
        return $this->hasMany(Code::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function code(string $type): Code
    {
        $code = $this->codes()->where('type', $type)->first();
        Truthy($code === null, "$type Code not Found");

        return $code;
    }

    public function createCode(string $type, int $length = 5, ?string $timeToExpire = '1:h'): static
    {
        $this->deleteCode($type);
        $code = $this->generate($type, $length);
        $this->codes()->create([
            'type' => $type,
            'code' => $code,
            'expire_at' => $this->expireAt($timeToExpire),
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
        $codes = $this->codes();
        for ($i = 0; $i < 10; $i++) {
            $code = $this->number($length);
            if (
                mb_strlen((string) $code) === $length &&
                ! $codes->where([['type', $type], ['code', $code]])->exists()
            ) {
                break;
            }
        }

        return (int) $code;
    }

    private function number(int $length = 5): int
    {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= random_int(0, 9);
        }

        return (int) $number;
    }

    private function expireAt(?string $timeToExpire): Carbon
    {
        if ($timeToExpire === null) {
            return now();
        }
        [$value, $unit] = explode(':', $timeToExpire);
        if (! in_array($unit, ['m', 'h', 'd'])) {
            return now();
        }

        return now()->add($unit, (int) $value);
    }
}
