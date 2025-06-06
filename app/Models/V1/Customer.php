<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Traits\FeesHandler;
use App\Traits\NotificationsHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Customer extends Model
{
    use FeesHandler;
    use HasFactory;
    use NotificationsHandler;

    protected $guarded = [];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }
}
