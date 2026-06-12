<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        // 🔥 เพิ่มตรงนี้
        'fullname',
        'organization',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['profile_photo_url'];

    // =========================
    // 🔥 RELATION
    // =========================
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // =========================
    // 🔥 CHECK ROLE
    // =========================
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // =========================
    // 🔥 PROFILE IMAGE
    // =========================
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : asset('images/default-avatar.png');
    }
}
