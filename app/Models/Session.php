<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'title',
        'description',
        'scheduled_at',
        'status',
        'meeting_link',
        'notes',
        'document_path',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function mentee()
    {
        return $this->belongsTo(Mentee::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
