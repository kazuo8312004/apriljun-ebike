<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'price',
        'cost_price',
        'brand',
        'model',
        'type',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function nwowUnits()
    {
        return $this->hasMany(NwowUnit::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function transferItems()
    {
        return $this->hasMany(TransferItem::class);
    }

    // Methods
    public function getTotalStockAcrossBranches()
    {
        return $this->inventories()->sum('stock_quantity');
    }

    public function getStockInBranch($branchId)
    {
        $inventory = $this->inventories()->where('branch_id', $branchId)->first();
        return $inventory ? $inventory->stock_quantity : 0;
    }

    public function getAvailableUnitsInBranch($branchId)
    {
        return $this->nwowUnits()
            ->where('branch_id', $branchId)
            ->where('status', 'in_stock')
            ->count();
    }

    public function isUnit()
    {
        return $this->type === 'unit';
    }
}
