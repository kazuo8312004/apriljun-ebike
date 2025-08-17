<?php
// app/Models/TransferItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'nwow_unit_id',
        'quantity',
        'notes'
    ];

    // Relationships
    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function nwowUnit()
    {
        return $this->belongsTo(NwowUnit::class);
    }
}
