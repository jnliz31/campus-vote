<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Voter extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'campus_email',
        'password',
        'course',
        'google_id',
        'student_id',
        'avatar',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    // Check if voter has voted in specific election
    public function hasVotedIn($electionId)
    {
        return $this->votes()->where('election_id', $electionId)->exists();
    }
}