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
        'is_seeded',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_seeded' => 'boolean',
    ];

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_STAFF = 'staff';

    const ROLE_LEVELS = [
        self::ROLE_STAFF => 1,
        self::ROLE_ADMIN => 2,
        self::ROLE_SUPER_ADMIN => 3,
    ];

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isMainAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isSeededAdmin()
    {
        return $this->is_seeded === true;
    }

    public function getRoleDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->role));
    }

    public function getRoleLevel()
    {
        return self::ROLE_LEVELS[$this->role] ?? 0;
    }

    public function canManage(Admin $targetAdmin)
    {
        if ($this->isSuperAdmin()) {
            return !($targetAdmin->isSeededAdmin() && $targetAdmin->isSuperAdmin() && $this->id !== $targetAdmin->id);
        }

        if ($this->isMainAdmin()) {
            return $targetAdmin->isStaff();
        }

        return false;
    }

    public function canDelete(Admin $targetAdmin)
{
    // You cannot delete yourself
    if ($this->id === $targetAdmin->id) {
        return false;
    }

    // Only prevent deletion of seeded accounts if they are super admins
    if ($targetAdmin->isSeededAdmin() && $targetAdmin->isSuperAdmin()) {
        return false;
    }


    if ($this->isSuperAdmin()) {
        return true;
    }

 
    return $this->canManage($targetAdmin);
}

    public function canChangePassword(Admin $targetAdmin)
    {
        if ($this->id === $targetAdmin->id) {
            return true;
        }

        return $this->canManage($targetAdmin);
    }

    public function getManageableRoles()
    {
        if ($this->isSuperAdmin()) {
            return [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_STAFF];
        }

        if ($this->isMainAdmin()) {
            return [self::ROLE_ADMIN, self::ROLE_STAFF];
        }

        return [];
    }
}