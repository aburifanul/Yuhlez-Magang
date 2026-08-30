<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'slug',
        'title',
        'short_description',
        'description',
        'registration_open_at',
        'registration_close_at',
        'program_start_at',
        'program_end_at',
    ];

    protected function casts(): array
    {
        return [
            'registration_open_at' => 'date',
            'registration_close_at' => 'date',
            'program_start_at' => 'date',
            'program_end_at' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function banners()
    {
        return $this->hasMany(InternshipProgramBanner::class, 'program_id');
    }

    public function positions()
    {
        return $this->hasMany(InternshipPosition::class, 'program_id');
    }

    public function registrations()
    {
        return $this->hasMany(InternshipRegistration::class, 'program_id');
    }

    public function participants()
    {
        return $this->hasMany(InternshipParticipant::class, 'program_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'program_id');
    }
}