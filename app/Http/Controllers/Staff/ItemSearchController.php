<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemSearchController extends Controller
{
    /**
     * Display items available for borrowing
     */
    public function index(Request $request)
    {
        $query = Item::where('available_stock', '>', 0)
            ->whereIn('condition', ['Baik', 'Rusak Ringan'])
            ->with('category', 'location');

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        $items = $query->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('staff.items.index', compact('items', 'categories'));
    }

    /**
     * Display single item detail
     */
    public function show(Item $item)
    {
        // Ensure item is available for borrowing
        if ($item->available_stock === 0 || !in_array($item->condition, ['Baik', 'Rusak Ringan'])) {
            abort(404);
        }

        $item->load('category', 'location');
        return view('staff.items.show', compact('item'));
    }

    /**
     * AJAX search endpoint
     */
    public function search(Request $request)
    {
        if (!$request->filled('q')) {
            return response()->json([]);
        }

        $search = $request->get('q');

        $items = Item::where('available_stock', '>', 0)
            ->whereIn('condition', ['Baik', 'Rusak Ringan'])
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->category->name,
                'available_stock' => $item->available_stock,
                'condition' => $item->condition,
                'url' => route('staff.items.show', $item),
            ];
        }));
    }
}
