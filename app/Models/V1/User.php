<?php

declare(strict_types=1);

namespace App\Models\V1;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\CodesManager;
use App\Enums\UserRoles;
use App\FirebaseNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use CodesManager;
    use FirebaseNotification;
    use HasFactory;

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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function badge()
    {
        return match ($this->role) {
            UserRoles::Producer->value => $this->hasOne(Producer::class),
            UserRoles::Carrier->value => $this->hasOne(Carrier::class),
            default => null
        };
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notifiable_id');
    }

    public function sendVerificationCode($by = 'phone'): static
    {
        $this->createCode('verification');
        $this->update([
            'verified_at' => null,
            'verified_by' => $by,
        ]);
        if ($by === 'phone') {
            $this->notifyFCM($this);
        } else {
            $this->notifyEmail($this);
        }

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
