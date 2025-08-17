<?php
// app/Models/Transfer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'from_branch_id',
        'to_branch_id',
        'user_id',
        'notes',
        'status',
        'transferred_at',
        'received_at',
        'received_by'
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // Relationships
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(TransferItem::class);
    }

    // Methods
    public static function generateTransferNumber()
    {
        $lastTransfer = self::latest()->first();
        $number = $lastTransfer ? (int) substr($lastTransfer->transfer_number, 2) + 1 : 1;
        return 'TR' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    public function canBeReceived()
    {
        return $this->status === 'in_transit';
    }
}
