<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'mentor_id',
        'mentee_id',
        'rating',
        'comment',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function mentee()
    {
        return $this->belongsTo(Mentee::class);
    }
}
