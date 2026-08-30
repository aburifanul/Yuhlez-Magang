<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'quota',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function program()
    {
        return $this->belongsTo(
            InternshipProgram::class,
            'program_id'
        );
    }

    public function registrations()
    {
        return $this->hasMany(
            InternshipRegistration::class,
            'position_id'
        );
    }
}