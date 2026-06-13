<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getConnectionName(): ?string
    {
        $default = config('database.default');
        $prefix = config("database.connections.{$default}.prefix", '');

        if ($prefix === '') {
            return null;
        }

        return match ($default) {
            'mysql', 'mariadb' => 'shared_mysql',
            'pgsql' => 'shared',
            default => null,
        };
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isPosStaff(): bool
    {
        return $this->role === 'staff';
    }
}
