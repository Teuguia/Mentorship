<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    public function mentee()
    {
        return $this->hasOne(Mentee::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
