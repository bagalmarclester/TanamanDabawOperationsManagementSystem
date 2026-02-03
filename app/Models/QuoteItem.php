<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'description', 'quantity', 'price', 'subtotal'
    ];
}