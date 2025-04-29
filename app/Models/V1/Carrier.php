<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Carrier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->where('belongTo_type', get_class($this));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(CarrierDetails::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'belongTo_id')
            ->where('belongTo_type', get_class($this));
    }

    public function profileImage(): HasOne
    {
        return $this->hasOne(Document::class, 'belongTo_id')
            ->where([['belongTo_type', get_class($this)], ['doc_type', 'profile']]);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'belongTo_id')
            ->where('belongTo_type', get_class($this));
    }

    public function validate(bool $state): bool
    {
        return $this->update(['is_valid' => $state]);
    }
}
