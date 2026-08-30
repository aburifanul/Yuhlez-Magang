<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'name',
        'short_description',
        'description',
        'logo',
        'whatsapp',
        'contact_email',
        'address',
        'gmap_embed',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function internshipPrograms()
    {
        return $this->hasMany(InternshipProgram::class);
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }
}