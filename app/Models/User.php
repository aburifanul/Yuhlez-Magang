<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'role',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_token_scope',
    ];

    protected $hidden = [
        'remember_token',
        'google_access_token',
        'google_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'google_token_expires_at' => 'datetime',

            // Token Google disimpan terenkripsi
            'google_access_token' => 'encrypted',
            'google_refresh_token' => 'encrypted',

            'role' => UserRole::class,

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
        return $this->hasOne(Company::class);
    }

    public function intern()
    {
        return $this->hasOne(Intern::class);
    }
}