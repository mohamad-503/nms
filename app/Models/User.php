<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'phone', 'employee_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function hasRole($roles): bool
    {
        if (!is_array($roles)) $roles = [$roles];
        return in_array($this->role, $roles);
    }

    public function canAccess($permission): bool
    {
        $matrix = [
            'super_admin' => ['*'],
            'manager' => ['customers','plans','billing','inventory','employees','tickets','routers','reports','logs','settings'],
            'accountant' => ['billing','reports','customers','invoices','expenses'],
            'technician' => ['tickets','routers','customers'],
            'employee' => ['customers','tickets'],
        ];
        $allowed = $matrix[$this->role] ?? [];
        return in_array('*', $allowed) || in_array($permission, $allowed);
    }
}
