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
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function canAccessBranch($branchId)
    {
        // Admin can access all branches
        if ($this->isAdmin()) {
            return true;
        }
        
        // Other users can only access their assigned branch
        return $this->branch_id == $branchId;
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