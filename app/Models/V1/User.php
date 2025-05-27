<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Enums\NotificationTypes;
use App\Enums\UserRoles;
use App\Traits\CodesHandler;
use App\Traits\NotificationsHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use CodesHandler;
    use HasFactory;
    use NotificationsHandler;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
            default => throw new \Exception("Invalid role: $this->role ")
        };
    }

    public function settings(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function sendVerificationCode($by = 'phone'): static
    {
        $this->createCode('verification');
        $this->update([
            'verified_at' => null,
            'verified_by' => $by,
        ]);
        $code = $this->code('verification')->code;
        $this->notify(
            title: 'verification code',
            body: "Your code is: $code",
            data: ['type' => NotificationTypes::verification->value, 'code' => $code],
            provider: $by,
        );

        return $this;
    }

    public function verify(): static
    {
        $this->deleteCode('verification');
        $this->update([
            'verified_at' => now(),
        ]);
        return $this;
    }
}
