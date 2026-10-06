<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_RECEPTIONIST = 'receptionist';
    public const ROLE_SECURITY = 'security';
    public const ROLE_HOST = 'host';
    public const ROLE_VIEWER = 'viewer';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_RECEPTIONIST => 'Receptionist',
        self::ROLE_SECURITY => 'Security',
        self::ROLE_HOST => 'Host / PIC',
        self::ROLE_VIEWER => 'Viewer',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'phone',
        'position',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(AppNotification::class)->where('is_read', false);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
