<?php
// app/Models/Loan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number',
        'branch_id',
        'user_id',
        'nwow_unit_id',
        'borrower_name',
        'borrower_phone',
        'borrower_address',
        'borrower_id_type',
        'borrower_id_number',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'collateral_amount',
        'loan_purpose',
        'notes',
        'status'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'collateral_amount' => 'decimal:2',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nwowUnit()
    {
        return $this->belongsTo(NwowUnit::class);
    }

    // Methods
    public static function generateLoanNumber($branchCode = null)
    {
        $prefix = 'LN' . ($branchCode ?: '');
        $lastLoan = self::where('loan_number', 'like', $prefix . '%')->latest()->first();
        $number = $lastLoan ? (int) substr($lastLoan->loan_number, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function isOverdue()
    {
        return $this->status === 'active' && Carbon::now()->gt($this->expected_return_date);
    }

    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->expected_return_date);
    }

    public function returnUnit()
    {
        $this->update([
            'status' => 'returned',
            'actual_return_date' => now()
        ]);

        $this->nwowUnit->returnToStock();
    }
}
