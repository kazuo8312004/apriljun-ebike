<?php
// app/Http/Controllers/LoanController.php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\NwowUnit;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['nwowUnit.product', 'branch', 'user']);

        // Filter by branch (if user is not admin)
        if (!Auth::user()->isAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_number', 'like', "%{$search}%")
                  ->orWhere('borrower_name', 'like', "%{$search}%")
                  ->orWhere('borrower_phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter overdue loans
        if ($request->filled('overdue')) {
            $query->where('status', 'active')
                  ->where('expected_return_date', '<', now());
        }

        $loans = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        return view('loans.index', compact('loans', 'branches'));
    }

    public function create()
    {
        $userBranch = Auth::user()->branch_id;
        $availableUnits = NwowUnit::with('product')
                                 ->where('branch_id', $userBranch)
                                 ->where('status', 'in_stock')
                                 ->get();

        return view('loans.create', compact('availableUnits', 'userBranch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nwow_unit_id' => 'required|exists:nwow_units,id',
            'borrower_name' => 'required|string|max:255',
            'borrower_phone' => 'required|string|max:20',
            'borrower_address' => 'required|string',
            'borrower_id_type' => 'required|string|max:100',
            'borrower_id_number' => 'required|string|max:50',
            'loan_date' => 'required|date',
            'expected_return_date' => 'required|date|after:loan_date',
            'collateral_amount' => 'required|numeric|min:0',
            'loan_purpose' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $unit = NwowUnit::findOrFail($validated['nwow_unit_id']);

        if ($unit->status !== 'in_stock' || $unit->branch_id != Auth::user()->branch_id) {
            return redirect()->route('loans.create')
                ->with('error', 'Selected unit is not available for loan.');
        }

        DB::transaction(function () use ($validated, $unit) {
            $loan = Loan::create([
                'loan_number' => Loan::generateLoanNumber(Auth::user()->branch->code),
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
                'nwow_unit_id' => $unit->id,
                'borrower_name' => $validated['borrower_name'],
                'borrower_phone' => $validated['borrower_phone'],
                'borrower_address' => $validated['borrower_address'],
                'borrower_id_type' => $validated['borrower_id_type'],
                'borrower_id_number' => $validated['borrower_id_number'],
                'loan_date' => $validated['loan_date'],
                'expected_return_date' => $validated['expected_return_date'],
                'collateral_amount' => $validated['collateral_amount'],
                'loan_purpose' => $validated['loan_purpose'],
                'notes' => $validated['notes'],
                'status' => 'active',
            ]);

            $unit->markAsLoaned();
        });

        return redirect()->route('loans.index')
            ->with('success', 'Loan created successfully!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['nwowUnit.product', 'branch', 'user']);
        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        if ($loan->status !== 'active') {
            return redirect()->route('loans.index')
                ->with('error', 'Only active loans can be edited.');
        }

        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        if ($loan->status !== 'active') {
            return redirect()->route('loans.index')
                ->with('error', 'Only active loans can be updated.');
        }

        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'borrower_phone' => 'required|string|max:20',
            'borrower_address' => 'required|string',
            'borrower_id_type' => 'required|string|max:100',
            'borrower_id_number' => 'required|string|max:50',
            'expected_return_date' => 'required|date|after:loan_date',
            'collateral_amount' => 'required|numeric|min:0',
            'loan_purpose' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $loan->update($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Loan updated successfully!');
    }

    public function return(Loan $loan)
    {
        if ($loan->status !== 'active') {
            return redirect()->route('loans.index')
                ->with('error', 'Loan is not active.');
        }

        $loan->returnUnit();

        return redirect()->route('loans.index')
            ->with('success', 'Unit returned successfully!');
    }

    public function markLost(Loan $loan)
    {
        if ($loan->status !== 'active') {
            return redirect()->route('loans.index')
                ->with('error', 'Loan is not active.');
        }

        $loan->update(['status' => 'lost']);

        return redirect()->route('loans.index')
            ->with('success', 'Loan marked as lost.');
    }
}
