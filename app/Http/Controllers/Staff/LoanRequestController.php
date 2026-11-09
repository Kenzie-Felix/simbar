<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanRequestController extends Controller
{
    /**
     * Display staff's loan requests
     */
    public function index(Request $request)
    {
        $query = Loan::where('user_id', auth()->id())
            ->with('loanItems.item', 'approver');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $loans = $query->latest('requested_at')->paginate(15);
        $statuses = [
            'Pending Approval' => 'Pending Approval',
            'Approved' => 'Approved',
            'Rejected' => 'Rejected',
            'Borrowed' => 'Borrowed',
            'Returned' => 'Returned',
        ];

        return view('staff.loans.index', compact('loans', 'statuses'));
    }

    /**
     * Show the form for creating a new loan request
     */
    public function create()
    {
        $items = Item::where('available_stock', '>', 0)
            ->whereIn('condition', ['Baik', 'Rusak Ringan'])
            ->orderBy('name')
            ->get();

        return view('staff.loans.create', compact('items'));
    }

    /**
     * Store a newly created loan request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id|distinct',
            'items.*.quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
            'planned_return_date' => 'required|date|after:today',
        ], [
            'items.required' => 'Please select at least one item.',
            'items.min' => 'Please select at least one item.',
            'items.*.item_id.required' => 'Item selection is required for each row.',
            'items.*.item_id.exists' => 'Selected item does not exist.',
            'items.*.item_id.distinct' => 'Cannot request the same item multiple times.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'purpose.required' => 'Purpose of loan is required.',
            'purpose.max' => 'Purpose may not exceed 500 characters.',
            'planned_return_date.required' => 'Expected return date is required.',
            'planned_return_date.date' => 'Invalid date format.',
            'planned_return_date.after' => 'Return date must be in the future.',
        ]);

        // Validate stock availability for all items
        foreach ($validated['items'] as $itemRequest) {
            $item = Item::find($itemRequest['item_id']);
            if (!$item || $item->available_stock < $itemRequest['quantity']) {
                return back()
                    ->withErrors(['items' => "Insufficient stock for {$item->name}"])
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Create the loan request
            $loan = Loan::create([
                'user_id' => auth()->id(),
                'status' => Loan::STATUS_PENDING,
                'purpose' => $validated['purpose'],
            ]);

            // Add items to the loan
            foreach ($validated['items'] as $itemRequest) {
                LoanItem::create([
                    'loan_id' => $loan->id,
                    'item_id' => $itemRequest['item_id'],
                    'quantity' => $itemRequest['quantity'],
                    'planned_return_at' => $validated['planned_return_date'],
                ]);
            }

            DB::commit();

            return redirect()->route('staff.loans.show', $loan)
                ->with('success', 'Loan request submitted successfully. Awaiting admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['general' => 'An error occurred while creating the loan request.'])
                ->withInput();
        }
    }

    /**
     * Display the specified loan request
     */
    public function show(Loan $loan)
    {
        // Ensure the user can only view their own loans
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('loanItems.item', 'approver');
        
        $statuses = [
            'Pending Approval' => 'Pending Approval',
            'Approved' => 'Approved',
            'Rejected' => 'Rejected',
            'Borrowed' => 'Borrowed',
            'Returned' => 'Returned',
        ];
        
        return view('staff.loans.show', compact('loan', 'statuses'));
    }

    /**
     * Get item details via AJAX
     */
    public function getItemDetails($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'category' => $item->category->name,
            'location' => $item->location->name,
            'available_stock' => $item->available_stock,
            'condition' => $item->condition,
        ]);
    }

    /**
     * Check item stock availability
     */
    public function checkAvailability(Request $request)
    {
        $itemId = $request->get('item_id');
        $quantity = $request->get('quantity', 1);

        $item = Item::find($itemId);

        if (!$item) {
            return response()->json(['available' => false, 'message' => 'Item not found']);
        }

        $available = $item->available_stock >= $quantity;

        return response()->json([
            'available' => $available,
            'required' => $quantity,
            'stock' => $item->available_stock,
            'message' => $available 
                ? "Available: {$item->available_stock} units"
                : "Only {$item->available_stock} units available",
        ]);
    }
}
