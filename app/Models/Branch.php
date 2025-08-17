<?php
// app/Models/Branch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'contact_number',
        'manager_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function nwowUnits()
    {
        return $this->hasMany(NwowUnit::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_branch_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_branch_id');
    }

    // Methods
    public function getStockByProduct($productId)
    {
        $inventory = $this->inventories()->where('product_id', $productId)->first();
        return $inventory ? $inventory->stock_quantity : 0;
    }

    public function getTotalUnitsInStock()
    {
        return $this->nwowUnits()->where('status', 'in_stock')->count();
    }
}
