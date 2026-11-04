<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanApprovalController extends Controller
{
    /**
     * Display loan approval dashboard
     */
    public function index(Request $request)
    {
        $query = Loan::with('user', 'loanItems.item', 'approver');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Search by staff name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $loans = $query->latest('requested_at')->paginate(20);

        $statuses = [
            'Menunggu Persetujuan' => 'Pending Approval',
            'Disetujui' => 'Approved',
            'Ditolak' => 'Rejected',
            'Dipinjam' => 'Borrowed',
            'Dikembalikan' => 'Returned',
        ];

        // Statistics
        $stats = [
            'pending_count' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'approved_count' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'rejected_count' => Loan::where('status', Loan::STATUS_REJECTED)->count(),
            'borrowed_count' => Loan::where('status', Loan::STATUS_BORROWED)->count(),
        ];

        return view('admin.loans.index', compact('loans', 'statuses', 'stats'), [
            'statusPending' => Loan::STATUS_PENDING,
        ]);
    }

    /**
     * Display specific loan request for approval
     */
    public function show(Loan $loan)
    {
        $loan->load('user', 'loanItems.item', 'approver');

        // Check stock availability before showing approval form
        $stockStatus = [];
        foreach ($loan->loanItems as $loanItem) {
            $stockStatus[$loanItem->id] = [
                'current' => $loanItem->item->available_stock,
                'requested' => $loanItem->quantity,
                'sufficient' => $loanItem->item->available_stock >= $loanItem->quantity,
            ];
        }

        return view('admin.loans.show', compact('loan', 'stockStatus'));
    }

    /**
     * Approve a loan request
     */
    public function approve(Request $request, Loan $loan)
    {
        // Validation
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if loan is already approved/rejected
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->withErrors([
                'approval' => "This loan request has already been {$loan->status}. Cannot approve again.",
            ]);
        }

        try {
            DB::beginTransaction();

            // Verify stock availability one more time
            foreach ($loan->loanItems as $loanItem) {
                $item = $loanItem->item;
                if ($item->available_stock < $loanItem->quantity) {
                    throw new \Exception(
                        "Insufficient stock for item '{$item->name}'. Only {$item->available_stock} available, {$loanItem->quantity} requested."
                    );
                }
            }

            // Update stock for each item
            foreach ($loan->loanItems as $loanItem) {
                $item = $loanItem->item;
                $quantity = $loanItem->quantity;

                // Decrement available stock
                $item->decrement('available_stock', $quantity);

                // Create stock movement record
                StockMovement::create([
                    'item_id' => $item->id,
                    'type' => 'Out',
                    'quantity' => $quantity,
                    'reference_type' => 'Loan',
                    'reference_id' => $loan->id,
                    'notes' => "Loan approval #{$loan->id} by " . auth()->user()->name . ($validated['notes'] ? ": {$validated['notes']}" : ''),
                    'created_by_id' => auth()->id(),
                ]);
            }

            // Update loan status
            $loan->approve(auth()->user());

            // Reload to get updated data
            $loan->refresh();

            DB::commit();

            return redirect()->route('admin.loans.show', $loan)
                ->with('success', "Loan request #{$loan->id} has been approved successfully. Stock has been decremented.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'approval' => "Error approving loan: {$e->getMessage()}",
            ]);
        }
    }

    /**
     * Show rejection form
     */
    public function rejectForm(Loan $loan)
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->withErrors([
                'rejection' => "This loan request has already been processed. Cannot reject now.",
            ]);
        }

        return view('admin.loans.reject', compact('loan'));
    }

    /**
     * Reject a loan request
     */
    public function reject(Request $request, Loan $loan)
    {
        // Validation
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.max' => 'Rejection reason may not exceed 500 characters.',
        ]);

        // Check if already processed
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->withErrors([
                'rejection' => "This loan request has already been processed.",
            ]);
        }

        try {
            DB::beginTransaction();

            // Update loan status
            $loan->reject($validated['rejection_reason'], auth()->user());

            DB::commit();

            return redirect()->route('admin.loans.show', $loan)
                ->with('success', "Loan request #{$loan->id} has been rejected. Staff member has been notified.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'rejection' => "Error rejecting loan: {$e->getMessage()}",
            ]);
        }
    }

    /**
     * Get approval statistics
     */
    public function getStats()
    {
        return response()->json([
            'pending' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'approved' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'rejected' => Loan::where('status', Loan::STATUS_REJECTED)->count(),
            'borrowed' => Loan::where('status', Loan::STATUS_BORROWED)->count(),
        ]);
    }

    /**
     * Export pending loans to CSV
     */
    public function exportPending()
    {
        $loans = Loan::where('status', Loan::STATUS_PENDING)
            ->with('user', 'loanItems.item')
            ->get();

        $csv = "Request ID,Staff Name,Staff Email,Items Count,Total Quantity,Purpose,Requested Date\n";

        foreach ($loans as $loan) {
            $csv .= "{$loan->id},";
            $csv .= "\"{$loan->user->name}\",";
            $csv .= "{$loan->user->email},";
            $csv .= "{$loan->getTotalItemsCount()},";
            $csv .= "{$loan->getTotalQuantity()},";
            $csv .= "\"" . str_replace('"', '""', $loan->purpose) . "\",";
            $csv .= "{$loan->requested_at->format('Y-m-d H:i')}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="pending_loans_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Bulk approve loans
     */
    public function bulkApprove(Request $request)
    {
        // ✅ FIXED: Parse JSON loan_ids string to array before validation
        $loanIds = $request->input('loan_ids');
        
        // If loan_ids is a JSON string, parse it
        if (is_string($loanIds)) {
            $loanIds = json_decode($loanIds, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $loanIds = [];
            }
        }
        
        // Merge parsed array back into request
        $request->merge(['loan_ids' => $loanIds]);
        
        $validated = $request->validate([
            'loan_ids' => 'required|array|min:1',
            'loan_ids.*' => 'exists:loans,id',
        ]);

        $results = [
            'approved' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($validated['loan_ids'] as $loanId) {
            try {
                $loan = Loan::findOrFail($loanId);

                if ($loan->status !== Loan::STATUS_PENDING) {
                    $results['failed']++;
                    $results['errors'][] = "Loan #{$loanId} is not pending";
                    continue;
                }

                DB::beginTransaction();

                // Verify and update stock
                foreach ($loan->loanItems as $loanItem) {
                    $item = $loanItem->item;
                    if ($item->available_stock < $loanItem->quantity) {
                        throw new \Exception("Insufficient stock for {$item->name}");
                    }

                    $item->decrement('available_stock', $loanItem->quantity);

                    StockMovement::create([
                        'item_id' => $item->id,
                        'type' => 'Out',
                        'quantity' => $loanItem->quantity,
                        'reference_type' => 'Loan',
                        'reference_id' => $loan->id,
                        'notes' => "Bulk approval by " . auth()->user()->name,
                        'created_by_id' => auth()->id(),
                    ]);
                }

                $loan->approve(auth()->user());
                DB::commit();
                $results['approved']++;
            } catch (\Exception $e) {
                DB::rollBack();
                $results['failed']++;
                $results['errors'][] = "Loan #{$loanId}: {$e->getMessage()}";
            }
        }

        return back()
            ->with('bulk_results', $results)
            ->with('success', "Bulk approval completed: {$results['approved']} approved, {$results['failed']} failed.");
    }
}
