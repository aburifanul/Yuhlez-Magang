<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'intern_id',
        'added_at',
        'removed_at',
    ];

    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}