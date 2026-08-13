<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['full_name', 'phone', 'department_id', 'position', 'salary', 'hire_date', 'status'];

    protected $casts = ['hire_date' => 'date', 'salary' => 'decimal:2'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
