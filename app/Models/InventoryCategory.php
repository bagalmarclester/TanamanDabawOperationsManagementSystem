<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryCategory::class);
    }
}