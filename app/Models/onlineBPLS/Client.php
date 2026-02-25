<?php
// app/Models/onlineBPLS/Client.php

namespace App\Models\onlineBPLS;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'client';

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'mobile_no',
        'password',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------

    public function applications()
    {
        return $this->hasMany(BplsApplication::class, 'client_id');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }
}