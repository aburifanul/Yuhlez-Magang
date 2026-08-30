<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intern extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'short_description',
        'description',
        'photo',
        'whatsapp',
        'contact_email',
        'address',
        'cv_path',
        'is_profile_complete',
    ];

    protected function casts(): array
    {
        return [
            'is_profile_complete' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(InternshipRegistration::class);
    }

    public function participants()
    {
        return $this->hasMany(InternshipParticipant::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function workMembers()
    {
        return $this->hasMany(WorkMember::class);
    }
}