<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'position_id',
        'intern_id',
        'status',
        'rejection_reason',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
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

    public function position()
    {
        return $this->belongsTo(
            InternshipPosition::class,
            'position_id'
        );
    }

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}