<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'issue_date',
        'due_date',
        'total_amount',
        'status'
    ];

    // Relationship: An invoice has many items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Relationship: An invoice belongs to a project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

 
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}