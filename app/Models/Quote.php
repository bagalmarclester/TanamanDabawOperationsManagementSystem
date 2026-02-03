<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'subject','quote_date', 'valid_until', 'total_amount', 'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }


    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
}