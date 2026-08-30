<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'program_id',
        'file_path',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    public function program()
    {
        return $this->belongsTo(
            InternshipProgram::class,
            'program_id'
        );
    }
}