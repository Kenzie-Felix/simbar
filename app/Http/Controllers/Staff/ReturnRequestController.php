<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\BorrowReturn;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    /**
     * Display staff's return requests
     */
    public function index(Request $request)
    {
        $query = BorrowReturn::whereHas('loan', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('loan.loanItems.item', 'returnItems.loanItem.item', 'verifier');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $returns = $query->latest('submitted_at')->paginate(15);

        $statuses = [
            'Menunggu Verifikasi' => 'Pending Verification',
            'Terima' => 'Accepted',
            'Tolak' => 'Rejected',
        ];

        return view('staff.returns.index', compact('returns', 'statuses'));
    }

    /**
     * Show the form for creating a new return request
     */
    public function create()
    {
        // Get staff's approved/borrowed loans
        $loans = Loan::where('user_id', auth()->id())
            ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])
            ->with('loanItems.item')
            ->get();

        if ($loans->isEmpty()) {
            return redirect()->route('staff.returns.index')
                ->with('info', 'You have no active loans to return.');
        }

        return view('staff.returns.create', compact('loans'));
    }

    /**
     * Store a newly created return request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'items' => 'required|array|min:1',
            'items.*.loan_item_id' => 'required|exists:loan_items,id|distinct',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
            'items.*.notes' => 'nullable|string|max:500',
            'return_notes' => 'nullable|string|max:1000',
        ], [
            'loan_id.required' => 'Please select a loan to return.',
            'loan_id.exists' => 'Selected loan does not exist.',
            'items.required' => 'Please select at least one item to return.',
            'items.min' => 'Please select at least one item to return.',
            'items.*.loan_item_id.required' => 'Item selection is required.',
            'items.*.loan_item_id.exists' => 'Selected item does not exist.',
            'items.*.loan_item_id.distinct' => 'Cannot return the same item multiple times.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.condition.required' => 'Item condition is required.',
            'items.*.condition.in' => 'Invalid condition selected.',
        ]);

        // Get the loan and verify it belongs to the user
        $loan = Loan::find($validated['loan_id']);

        if ($loan->user_id !== auth()->id()) {
            return back()->withErrors(['loan_id' => 'Unauthorized loan access.']);
        }

        if (!in_array($loan->status, [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])) {
            return back()->withErrors(['loan_id' => 'This loan cannot be returned at this time.']);
        }

        try {
            DB::beginTransaction();

            // Verify quantities don't exceed borrowed amounts
            foreach ($validated['items'] as $itemRequest) {
                $loanItem = LoanItem::find($itemRequest['loan_item_id']);

                // Verify loan_item belongs to this loan
                if ($loanItem->loan_id !== $loan->id) {
                    throw new \Exception("Item {$itemRequest['loan_item_id']} does not belong to this loan.");
                }

                // Check quantity doesn't exceed borrowed
                if ($itemRequest['quantity'] > $loanItem->quantity) {
                    throw new \Exception(
                        "Cannot return more {$loanItem->item->name} than borrowed. Borrowed: {$loanItem->quantity}, Returning: {$itemRequest['quantity']}"
                    );
                }

                // Check if already fully returned
                if ($loanItem->returned_at && $loanItem->return_quantity === $loanItem->quantity) {
                    throw new \Exception("Item {$loanItem->item->name} has already been fully returned.");
                }
            }

            // Create return request
            $return = BorrowReturn::create([
                'loan_id' => $loan->id,
                'status' => BorrowReturn::STATUS_PENDING,
                'submitted_at' => now(),
            ]);

            // Add return items
            foreach ($validated['items'] as $itemRequest) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'loan_item_id' => $itemRequest['loan_item_id'],
                    'quantity' => $itemRequest['quantity'],
                    'condition' => $itemRequest['condition'],
                    'notes' => $itemRequest['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.returns.show', $return)
                ->with('success', 'Return request submitted successfully. Awaiting admin verification.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['general' => "Error creating return request: {$e->getMessage()}"])
                ->withInput();
        }
    }

    /**
     * Display the specified return request
     */
    public function show(BorrowReturn $return)
    {
        // Ensure the user can only view their own returns
        if ($return->loan->user_id !== auth()->id()) {
            abort(403);
        }

        $return->load('loan.user', 'returnItems.loanItem.item', 'verifier');

        return view('staff.returns.show', compact('return'));
    }

    /**
     * Get loan details via AJAX
     */
    public function getLoanDetails($id)
    {
        $loan = Loan::find($id);

        if (!$loan || $loan->user_id !== auth()->id()) {
            return response()->json(['error' => 'Loan not found'], 404);
        }

        $loanItems = $loan->loanItems()->with('item')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'item_id' => $item->item_id,
                'item_code' => $item->item->code,
                'item_name' => $item->item->name,
                'quantity' => $item->quantity,
                'returned_quantity' => $item->return_quantity ?? 0,
                'remaining' => $item->quantity - ($item->return_quantity ?? 0),
                'condition' => $item->item->condition,
            ];
        });

        return response()->json([
            'loan_id' => $loan->id,
            'loan_status' => $loan->status,
            'items' => $loanItems,
        ]);
    }
}
