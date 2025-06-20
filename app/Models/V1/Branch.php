<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Branch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
