<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
protected $fillable = ['username', 'password', 'role'];
protected $hidden = ['password'];
}

