<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workshop_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => \App\Enums\UserRole::class,
        ];
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function isOwner(): bool
    {
        return $this->role === \App\Enums\UserRole::OWNER;
    }

    public function isFrontDesk(): bool
    {
        return $this->role === \App\Enums\UserRole::FRONT_DESK;
    }

    public function isTechnician(): bool
    {
        return $this->role === \App\Enums\UserRole::TECHNICIAN;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'workshop_id' => $this->workshop_id,
            'role' => $this->role?->value ?? $this->role,
        ];
    }
}
