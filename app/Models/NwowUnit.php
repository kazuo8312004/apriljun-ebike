<?php
// app/Models/NwowUnit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NwowUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'chassis_no',
        'motor_no',
        'battery_no',
        'controller_no',
        'charger_no',
        'remote_no',
        'color',
        'purchase_date',
        'purchase_price',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function transferItems()
    {
        return $this->hasMany(TransferItem::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === 'in_stock';
    }

    public function markAsSold()
    {
        $this->update(['status' => 'sold']);
    }

    public function markAsLoaned()
    {
        $this->update(['status' => 'loaned']);
    }

    public function markAsTransferred()
    {
        $this->update(['status' => 'transferred']);
    }

    public function returnToStock()
    {
        $this->update(['status' => 'in_stock']);
    }
}
