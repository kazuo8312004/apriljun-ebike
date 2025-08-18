<?php
// app/Models/User.php - UPDATED with missing methods

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // FIXED: Missing Methods
    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can access a specific branch.
     */
    public function canAccessBranch($branchId)
    {
        return $this->isAdmin() || $this->branch_id == $branchId;
    }

    /**
     * Check if user is a manager.
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function canManageBranch($branchId)
    {
        return $this->isAdmin() || ($this->isManager() && $this->branch_id == $branchId);
    }

    public function getBranchCode()
    {
        return $this->branch ? $this->branch->code : null;
    }
}
