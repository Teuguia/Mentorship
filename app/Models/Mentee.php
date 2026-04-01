<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goals',
        'profession',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domains()
    {
        return $this->belongsToMany(Domain::class, 'domain_mentee')->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
