<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
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
