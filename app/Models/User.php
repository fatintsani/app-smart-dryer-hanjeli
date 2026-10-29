<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'status',
        'approved_at',
        'phone',
        'avatar',
        'google_id',
        'password',
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
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper to check if user is Admin
     */
    public function isAdmin(): bool
    {
        return strtoupper($this->role ?? '') === 'ADMIN';
    }

    /**
     * Helper to check if user is approved by Administrator
     */
    public function isApproved(): bool
    {
        return strtoupper($this->status ?? 'PENDING') === 'APPROVED';
    }

    /**
     * Helper to check if user is waiting for approval
     */
    public function isPending(): bool
    {
        return strtoupper($this->status ?? 'PENDING') === 'PENDING';
    }

    /**
     * Helper to check if user registration was rejected
     */
    public function isRejected(): bool
    {
        return strtoupper($this->status ?? '') === 'REJECTED';
    }

    /**
     * Format user payload for Frontend responses
     */
    public function toAuthPayload(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'username' => $this->username ?: (explode('@', $this->email)[0] ?? ''),
            'email' => $this->email,
            'role' => strtoupper($this->role ?: 'OPERATOR'),
            'status' => strtoupper($this->status ?: 'PENDING'),
            'isApproved' => $this->isApproved(),
            'approvedAt' => $this->approved_at?->toISOString(),
            'phone' => $this->phone,
            'avatarUrl' => $this->avatar,
            'googleId' => $this->google_id,
            'createdAt' => $this->created_at?->toISOString(),
        ];
    }
}
