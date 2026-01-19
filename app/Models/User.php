<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // Tell Laravel to use 'uname' as the username field for authentication
    public function username()
    {
        return 'uname';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uname',
        'password',
        'employee_id',
        'encoded_by',
        'encoded_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'encoded_date' => 'datetime', // ADD THIS LINE
            'password' => 'hashed',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function encodedBy()
    {
        return $this->belongsTo(EmployeeInfo::class, 'encoded_by');
    }

    /**
     * Accessor for email from employee info.
     */
    public function getEmailAttribute()
    {
        return $this->employee->email ?? null;
    }
}