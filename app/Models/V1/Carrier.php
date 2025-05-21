<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Traits\FeesHandler;
use App\Traits\ImagesHandler;
use App\Traits\NotificationsHandler;
use App\Traits\ReviewsHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Carrier extends Model
{
    use FeesHandler;
    use HasFactory;
    use ImagesHandler;
    use NotificationsHandler;
    use ReviewsHandler;

    protected $guarded = [];

    public function getFirebaseTokenAttribute()
    {
        return $this->user->firebase_token;
    }

    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(CarrierDetails::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function validate(bool $state): bool
    {
        return $this->update(['is_valid' => $state]);
    }
}
