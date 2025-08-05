<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Role constants for better code maintainability
    const ROLE_MAIN_ADMIN = 'main_admin';
    const ROLE_STAFF = 'staff';

    public function isMainAdmin()
    {
        return $this->role === self::ROLE_MAIN_ADMIN;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function getRoleDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->role));
    }
}