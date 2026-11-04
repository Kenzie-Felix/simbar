<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Show stock in form
     */
    public function showStockInForm()
    {
        $items = Item::orderBy('name')->get();
        return view('admin.stock.stock-in', compact('items'));
    }

    /**
     * Record stock in transaction
     */
    public function recordStockIn(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'reference_type' => 'required|in:Purchase,Transfer,Return,Other',
            'notes' => 'nullable|string|max:500',
        ], [
            'item_id.required' => 'Item is required.',
            'item_id.exists' => 'Selected item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'reference_type.required' => 'Reference type is required.',
            'reference_type.in' => 'Selected reference type is invalid.',
            'notes.max' => 'Notes may not be greater than 500 characters.',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Update stock
        $item->updateStockIn(
            $validated['quantity'],
            $validated['notes'] ?? null,
            auth()->user(),
            $validated['reference_type']
        );

        return redirect()->route('admin.stock.in')
            ->with('success', "Stock added: {$validated['quantity']} units of {$item->name}");
    }

    /**
     * Show stock adjustment form
     */
    public function showStockAdjustmentForm()
    {
        $items = Item::orderBy('name')->get();
        return view('admin.stock.adjustment', compact('items'));
    }

    /**
     * Record stock adjustment
     */
    public function recordStockAdjustment(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer',
            'reason' => 'required|in:Damage,Loss,Expiry,Correction,Other',
            'notes' => 'nullable|string|max:500',
        ], [
            'item_id.required' => 'Item is required.',
            'item_id.exists' => 'Selected item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'reason.required' => 'Reason is required.',
            'reason.in' => 'Selected reason is invalid.',
            'notes.max' => 'Notes may not be greater than 500 characters.',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Validate that we don't go below zero
        $newAvailableStock = $item->available_stock + $validated['quantity'];
        if ($newAvailableStock < 0) {
            return back()->withErrors(['quantity' => 'Adjustment would result in negative stock.']);
        }

        // Record movement
        $notes = "Adjustment ({$validated['reason']})" . ($validated['notes'] ? ": {$validated['notes']}" : '');
        StockMovement::create([
            'item_id' => $item->id,
            'type' => $validated['quantity'] > 0 ? 'In' : 'Out',
            'quantity' => abs($validated['quantity']),
            'reference_type' => 'Adjustment',
            'notes' => $notes,
            'created_by_id' => auth()->id(),
        ]);

        // Update stock
        $item->increment('available_stock', $validated['quantity']);
        if ($validated['quantity'] > 0) {
            $item->increment('total_stock', $validated['quantity']);
        } else {
            $item->decrement('total_stock', abs($validated['quantity']));
        }

        return redirect()->route('admin.stock.adjustment')
            ->with('success', "Stock adjustment recorded for {$item->name}");
    }

    /**
     * View stock movements history
     */
    public function history(Request $request)
    {
        $query = StockMovement::with('item', 'createdBy');

        // Filter by item if provided
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest('created_at')->paginate(20);
        $items = Item::orderBy('name')->get();

        return view('admin.stock.history', compact('movements', 'items'));
    }
}
