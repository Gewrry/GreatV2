<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'email',
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'gender',
        'contact_number',
        'employee_address',
        'hire_date',
        'end_of_contract_date',
        'employee_group',
        'designation',
        'employee_remarks',
        'biometrics_no',
        'rate_per_day',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'hire_date' => 'date',
        'end_of_contract_date' => 'date',
        'rate_per_day' => 'decimal:2',
        'biometrics_no' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'birthday',
        'hire_date',
        'end_of_contract_date',
    ];

    /**
     * Get the department that the employee belongs to.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Get the employee's name with ID.
     *
     * @return string
     */
    public function getNameWithIdAttribute(): string
    {
        return "{$this->employee_id} - {$this->first_name} {$this->last_name}";
    }

    /**
     * Scope a query to only include active employees (not yet end of contract).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_of_contract_date')
                ->orWhere('end_of_contract_date', '>', now());
        });
    }

    /**
     * Scope a query to only include employees by department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to search employees by name or ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('employee_id', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }
    
}