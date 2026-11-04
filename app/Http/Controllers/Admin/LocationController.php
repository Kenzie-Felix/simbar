<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of locations
     */
    public function index()
    {
        $locations = Location::withCount('items')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created location in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:locations,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Location name is required.',
            'name.unique' => 'This location name already exists.',
            'name.max' => 'Location name may not be greater than 100 characters.',
            'description.max' => 'Description may not be greater than 500 characters.',
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location
     */
    public function show(Location $location)
    {
        $location->load('items');
        return view('admin.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified location in database
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => "required|string|max:100|unique:locations,name,{$location->id}",
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Location name is required.',
            'name.unique' => 'This location name already exists.',
            'name.max' => 'Location name may not be greater than 100 characters.',
            'description.max' => 'Description may not be greater than 500 characters.',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location from database
     */
    public function destroy(Location $location)
    {
        // Check if location has items
        if ($location->items()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location that has items. Please reassign or delete the items first.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
