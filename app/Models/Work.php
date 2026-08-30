<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'slug',
        'title',
        'short_description',
        'description',
    ];

    protected function casts(): array
    {
        return [
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

    public function photos()
    {
        return $this->hasMany(WorkPhoto::class, 'work_id');
    }

    public function members()
    {
        return $this->hasMany(WorkMember::class, 'work_id');
    }
}