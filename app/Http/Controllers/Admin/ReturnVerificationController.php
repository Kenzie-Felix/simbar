<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowReturn;
use App\Models\ReturnItem;
use App\Models\LoanItem;
use App\Models\Loan;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnVerificationController extends Controller
{
    /**
     * Display return verification dashboard
     */
    public function index(Request $request)
    {
        $query = BorrowReturn::with('loan.user', 'returnItems.loanItem.item', 'verifier');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Search by staff name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('loan.user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $returns = $query->latest('submitted_at')->paginate(20);

        $statuses = [
            'Pending Verification' => 'Pending Verification',
            'Accepted' => 'Accepted',
            'Rejected' => 'Rejected',
        ];

        // Statistics
        $stats = [
            'pending_count' => BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)->count(),
            'accepted_count' => BorrowReturn::where('status', BorrowReturn::STATUS_ACCEPTED)->count(),
            'rejected_count' => BorrowReturn::where('status', BorrowReturn::STATUS_REJECTED)->count(),
        ];

        return view('admin.returns.index', compact('returns', 'statuses', 'stats'));
    }

    /**
     * Display specific return request for verification
     */
    public function show(BorrowReturn $return)
    {
        $return->load('loan.user', 'returnItems.loanItem.item', 'verifier');

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Accept a return request and update stock/conditions
     */
    public function verify(Request $request, BorrowReturn $return)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Check if already verified/rejected
        if ($return->status !== BorrowReturn::STATUS_PENDING) {
            return back()->withErrors([
                'verification' => "This return has already been {$return->status}. Cannot verify again.",
            ]);
        }

        try {
            DB::beginTransaction();

            // Process each returned item
            foreach ($return->returnItems as $returnItem) {
                $loanItem = $returnItem->loanItem;
                $item = $loanItem->item;

                // Update item condition if it changed and is damaged/lost
                if ($returnItem->condition !== $item->condition) {
                    $item->updateCondition(
                        $returnItem->condition,
                        "Return verification for Loan #{$return->loan_id}",
                        auth()->user()
                    );
                }

                // Increment available stock based on item condition
                // Only count as available if condition is Baik or Rusak Ringan
                if (in_array($returnItem->condition, ['Baik', 'Rusak Ringan'])) {
                    $item->increment('available_stock', $returnItem->quantity);
                }

                // Create stock movement record (In - for return)
                StockMovement::create([
                    'item_id' => $item->id,
                    'type' => 'In',
                    'quantity' => $returnItem->quantity,
                    'reference_type' => 'Return',
                    'reference_id' => $return->id,
                    'notes' => "Return verification for Loan #{$return->loan_id}. Item condition: {$returnItem->condition}" . 
                               ($validated['admin_notes'] ? ". Admin notes: {$validated['admin_notes']}" : ''),
                    'created_by_id' => auth()->id(),
                ]);

                // Update loan item with returned quantity and condition
                $loanItem->update([
                    'returned_at' => now(),
                    'return_quantity' => $returnItem->quantity,
                    'return_condition' => $returnItem->condition,
                ]);
            }

            // Update return status
            $return->verify(auth()->user());

            // Check if all items from loan have been returned
            $loan = $return->loan;
            $allReturned = $loan->loanItems()
                ->whereNull('returned_at')
                ->doesntExist();

            // Update loan status if all items returned
            if ($allReturned) {
                $loan->update(['status' => Loan::STATUS_RETURNED]);
            }

            DB::commit();

            return redirect()->route('admin.returns.show', $return)
                ->with('success', "Return request #{$return->id} has been verified and accepted. Stock has been restored.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'verification' => "Error verifying return: {$e->getMessage()}",
            ]);
        }
    }

    /**
     * Show rejection form
     */
    public function rejectForm(BorrowReturn $return)
    {
        if ($return->status !== BorrowReturn::STATUS_PENDING) {
            return back()->withErrors([
                'rejection' => "This return has already been processed.",
            ]);
        }

        return view('admin.returns.reject', compact('return'));
    }

    /**
     * Reject a return request
     */
    public function reject(Request $request, BorrowReturn $return)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.max' => 'Rejection reason may not exceed 500 characters.',
        ]);

        // Check if already verified/rejected
        if ($return->status !== BorrowReturn::STATUS_PENDING) {
            return back()->withErrors([
                'rejection' => "This return has already been processed.",
            ]);
        }

        try {
            DB::beginTransaction();

            // Update return status (rejection)
            $return->reject($validated['rejection_reason'], auth()->user());

            DB::commit();

            return redirect()->route('admin.returns.show', $return)
                ->with('success', "Return request #{$return->id} has been rejected.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'rejection' => "Error rejecting return: {$e->getMessage()}",
            ]);
        }
    }

    /**
     * Get return statistics via AJAX
     */
    public function getStats()
    {
        return response()->json([
            'pending' => BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)->count(),
            'accepted' => BorrowReturn::where('status', BorrowReturn::STATUS_ACCEPTED)->count(),
            'rejected' => BorrowReturn::where('status', BorrowReturn::STATUS_REJECTED)->count(),
        ]);
    }

    /**
     * Export pending returns to CSV
     */
    public function exportPending()
    {
        $returns = BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)
            ->with('loan.user', 'returnItems.loanItem.item')
            ->get();

        $csv = "Return ID,Staff Name,Staff Email,Items Count,Total Quantity,Submitted Date\n";

        foreach ($returns as $return) {
            $csv .= "{$return->id},";
            $csv .= "\"{$return->loan->user->name}\",";
            $csv .= "{$return->loan->user->email},";
            $csv .= "{$return->getReturnedItemsCount()},";
            $csv .= "{$return->getTotalReturnedQuantity()},";
            $csv .= "{$return->submitted_at->format('Y-m-d H:i')}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="pending_returns_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }
}
