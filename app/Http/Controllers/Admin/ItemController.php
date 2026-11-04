<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of items
     */
    public function index()
    {
        $items = Item::with('category', 'location')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('admin.items.create', compact('categories', 'locations'));
    }

    /**
     * Store a newly created item in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'total_stock' => 'required|integer|min:0',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
        ], [
            'code.required' => 'Item code is required.',
            'code.unique' => 'This item code already exists.',
            'code.max' => 'Item code may not be greater than 50 characters.',
            'name.required' => 'Item name is required.',
            'name.max' => 'Item name may not be greater than 200 characters.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'location_id.required' => 'Location is required.',
            'location_id.exists' => 'Selected location does not exist.',
            'total_stock.required' => 'Stock quantity is required.',
            'total_stock.integer' => 'Stock quantity must be a number.',
            'total_stock.min' => 'Stock quantity cannot be negative.',
            'condition.required' => 'Item condition is required.',
            'condition.in' => 'Selected condition is invalid.',
        ]);

        // Set available stock equal to total stock initially
        $validated['available_stock'] = $validated['total_stock'];

        Item::create($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified item
     */
    public function show(Item $item)
    {
        $item->load('category', 'location', 'conditionLogs', 'stockMovements');
        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories', 'locations'));
    }

    /**
     * Update the specified item in database
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code' => "required|string|max:50|unique:items,code,{$item->id}",
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'total_stock' => 'required|integer|min:0',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
        ], [
            'code.required' => 'Item code is required.',
            'code.unique' => 'This item code already exists.',
            'code.max' => 'Item code may not be greater than 50 characters.',
            'name.required' => 'Item name is required.',
            'name.max' => 'Item name may not be greater than 200 characters.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'location_id.required' => 'Location is required.',
            'location_id.exists' => 'Selected location does not exist.',
            'total_stock.required' => 'Stock quantity is required.',
            'total_stock.integer' => 'Stock quantity must be a number.',
            'total_stock.min' => 'Stock quantity cannot be negative.',
            'condition.required' => 'Item condition is required.',
            'condition.in' => 'Selected condition is invalid.',
        ]);

        $item->update($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from database
     */
    public function destroy(Item $item)
    {
        // Check if item has active loans
        if ($item->loanItems()->whereHas('loan', function ($q) {
            $q->whereIn('status', ['Disetujui', 'Dipinjam']);
        })->count() > 0) {
            return redirect()->route('admin.items.index')
                ->with('error', 'Cannot delete item with active loans. Please resolve all loans first.');
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Item deleted successfully.');
    }
}
