<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'inventory';
    protected $fillable = [
        'category_id',
        'item_name',
        'sku',
        'price',
        'stock_level',
    ];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}