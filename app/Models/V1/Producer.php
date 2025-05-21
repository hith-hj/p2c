<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Traits\FeesHandler;
use App\Traits\NotificationsHandler;
use App\Traits\ReviewsHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producer extends Model
{
    use FeesHandler;
    use HasFactory;
    use NotificationsHandler;
    use ReviewsHandler;

    protected $guarded = [];

    public function getFirebaseTokenAttribute()
    {
        return $this->user->firebase_token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
