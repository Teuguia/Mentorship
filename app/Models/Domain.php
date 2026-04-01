<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'domain_mentor')->withTimestamps();
    }

    public function mentees()
    {
        return $this->belongsToMany(Mentee::class, 'domain_mentee')->withTimestamps();
    }
}
