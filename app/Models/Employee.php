<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected static function booted()
    {
        static::deleting(function ($employee) {
            // Check if the user exists to avoid errors
            if ($employee->user) { 
                $employee->user->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
