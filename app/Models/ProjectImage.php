<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectImage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'image_path',
        'project_id',
    ];

}
