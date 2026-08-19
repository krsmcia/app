<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'department_users'
        )->withTimestamps();
    }
    public function departmentUsers()
    {
        return $this->hasMany(DepartmentUser::class);
    }
}
