<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'project_name',
        'project_budget',
        'is_active',
        'project_start_date',
        'project_end_date',
        'client_id',
        'assigned_user_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }
}
