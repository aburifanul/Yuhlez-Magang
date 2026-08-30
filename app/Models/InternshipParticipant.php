<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'intern_id',
        'joined_at',
        'removed_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'removed_at' => 'datetime',
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

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}