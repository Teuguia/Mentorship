<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'expertise_title',
        'years_experience',
        'hourly_rate',
        'availability',
        'profile_photo',
        'verification_status',
        'verification_document',
        'verification_document_name',
        'verification_document_mime',
        'verification_document_size',
        'linkedin_url',
        'portfolio_url',
        'verification_notes',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'verification_document',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domains()
    {
        return $this->belongsToMany(Domain::class, 'domain_mentor')->withTimestamps();
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

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function verificationStatusLabel(): string
    {
        return match ($this->verification_status) {
            'verified' => 'Valide',
            'rejected' => 'A corriger',
            default => 'En attente',
        };
    }
}
