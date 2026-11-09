<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
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
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP.',
            'image.max' => 'Image size must not exceed 5 MB.',
            'total_stock.required' => 'Stock quantity is required.',
            'total_stock.integer' => 'Stock quantity must be a number.',
            'total_stock.min' => 'Stock quantity cannot be negative.',
            'condition.required' => 'Item condition is required.',
            'condition.in' => 'Selected condition is invalid.',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // Set available stock equal to total stock initially
        $validated['available_stock'] = $validated['total_stock'];
        $validated['image_path'] = $imagePath;

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
     * Handle image preview (for AJAX preview before save)
     */
    public function previewImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image provided'], 400);
        }

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP.',
            'image.max' => 'Image size must not exceed 5 MB.',
        ]);

        // Create temporary URL for preview
        $tempPath = $request->file('image')->getRealPath();
        $base64 = base64_encode(file_get_contents($tempPath));
        $mimeType = $request->file('image')->getMimeType();

        return response()->json([
            'success' => true,
            'preview' => 'data:' . $mimeType . ';base64,' . $base64,
            'filename' => $request->file('image')->getClientOriginalName(),
        ]);
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
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
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP.',
            'image.max' => 'Image size must not exceed 5 MB.',
            'total_stock.required' => 'Stock quantity is required.',
            'total_stock.integer' => 'Stock quantity must be a number.',
            'total_stock.min' => 'Stock quantity cannot be negative.',
            'condition.required' => 'Item condition is required.',
            'condition.in' => 'Selected condition is invalid.',
        ]);

        // Handle image deletion if requested
        if ($request->input('remove_current_image')) {
            if ($item->image_path) {
                \Storage::disk('public')->delete($item->image_path);
                $validated['image_path'] = null;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image_path) {
                \Storage::disk('public')->delete($item->image_path);
            }
            // Upload new image
            $validated['image_path'] = $request->file('image')->store('items', 'public');
        }

        // Handle stock quantity changes - CRITICAL: Properly manage available_stock when total_stock changes
        $newTotalStock = $validated['total_stock'];
        $oldTotalStock = $item->total_stock;
        $stockDifference = $newTotalStock - $oldTotalStock;

        // If total_stock changed, adjust available_stock accordingly
        // Formula: available_stock + borrowed_stock = total_stock (always true)
        // When increasing total_stock: add the difference to available_stock
        // When decreasing total_stock: subtract from available_stock (but never go negative or below borrowed items)
        if ($stockDifference !== 0) {
            $borrowedStock = $oldTotalStock - $item->available_stock;

            // Validate: cannot reduce total_stock below currently borrowed items
            if ($newTotalStock < $borrowedStock) {
                return redirect()->route('admin.items.edit', $item)
                    ->withInput()
                    ->withErrors([
                        'total_stock' => "Cannot reduce total stock to {$newTotalStock}. Currently {$borrowedStock} units are borrowed. Minimum allowed: {$borrowedStock}",
                    ]);
            }

            // Calculate new available stock
            $newAvailableStock = $newTotalStock - $borrowedStock;
            $validated['available_stock'] = $newAvailableStock;
        }

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

        // Delete image if exists
        if ($item->image_path) {
            \Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Item deleted successfully.');
    }
}
