<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Code extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'code' => 'int',
            'expire_at' => 'datetime',
        ];
    }

    public function isValid(): bool
    {
        return $this->expire_at > now();
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }
}
