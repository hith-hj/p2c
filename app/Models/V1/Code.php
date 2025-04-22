<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Code extends Model
{
    protected $guarded = [];

    protected $casts = ['code' => 'integer'];

    public function holder(): BelongsTo
    {
        return $this->belongsTo($this->belongTo_type)
            ->where('id', $this->belongTo_id);
    }
}
