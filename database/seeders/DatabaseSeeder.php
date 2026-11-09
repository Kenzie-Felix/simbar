<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update admin user (prevent duplicates)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'Admin',
                'is_active' => true,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Create or update staff user (prevent duplicates)
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'role' => 'Staff',
                'is_active' => true,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Create categories
        $categories = [
            Category::create(['name' => 'Electronics', 'description' => 'Electronic equipment']),
            Category::create(['name' => 'Furniture', 'description' => 'Office furniture']),
            Category::create(['name' => 'Tools', 'description' => 'Tools and equipment']),
        ];

        // Create locations
        $locations = [
            Location::create(['name' => 'Warehouse A', 'description' => 'Main warehouse']),
            Location::create(['name' => 'Office Floor 1', 'description' => 'First floor storage']),
            Location::create(['name' => 'Office Floor 2', 'description' => 'Second floor storage']),
        ];

        // Create sample items
        Item::create([
            'code' => 'ELEC-001',
            'name' => 'Projector',
            'category_id' => $categories[0]->id,
            'location_id' => $locations[0]->id,
            'description' => 'Conference room projector',
            'total_stock' => 5,
            'available_stock' => 5,
            'condition' => 'Baik',
        ]);

        Item::create([
            'code' => 'ELEC-002',
            'name' => 'Laptop Computer',
            'category_id' => $categories[0]->id,
            'location_id' => $locations[0]->id,
            'description' => 'Work laptop',
            'total_stock' => 10,
            'available_stock' => 8,
            'condition' => 'Baik',
        ]);

        Item::create([
            'code' => 'FURN-001',
            'name' => 'Office Chair',
            'category_id' => $categories[1]->id,
            'location_id' => $locations[1]->id,
            'description' => 'Ergonomic office chair',
            'total_stock' => 20,
            'available_stock' => 15,
            'condition' => 'Rusak Ringan',
        ]);

        Item::create([
            'code' => 'TOOL-001',
            'name' => 'Drill Machine',
            'category_id' => $categories[2]->id,
            'location_id' => $locations[2]->id,
            'description' => 'Power drill machine',
            'total_stock' => 3,
            'available_stock' => 3,
            'condition' => 'Baik',
        ]);

        Item::create([
            'code' => 'ELEC-003',
            'name' => 'USB Cables',
            'category_id' => $categories[0]->id,
            'location_id' => $locations[0]->id,
            'description' => 'Various USB cables',
            'total_stock' => 50,
            'available_stock' => 40,
            'condition' => 'Baik',
        ]);
    }
}

