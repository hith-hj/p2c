<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Enums\UserRoles;
use App\Traits\CodesHandler;
use App\Traits\NotificationsHandler;
use App\Traits\VerificationHandler;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

final class User extends Authenticatable implements JWTSubject
{
    use CodesHandler;
    use HasFactory;
    use NotificationsHandler;
    use VerificationHandler;

    protected $fillable = [
        'email',
        'password',
        'phone',
        'firebase_token',
        'role',
        'verification_code',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier(): int|string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function badge(): HasOne
    {
        return match ($this->role) {
            UserRoles::Producer->value => $this->hasOne(Producer::class),
            UserRoles::Carrier->value => $this->hasOne(Carrier::class),
            default => throw new Exception("Invalid role: $this->role ")
        };
    }

    public function settings(): HasOne
    {
        return $this->hasOne(Setting::class);
    }
}
