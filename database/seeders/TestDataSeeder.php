<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\BorrowReturn;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@simbar.local',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'Admin',
            'is_active' => true,
        ]);

        // Create staff users
        $staff1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@simbar.local',
            'password' => Hash::make('BudiPass123!'),
            'role' => 'Staff',
            'is_active' => true,
        ]);

        $staff2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@simbar.local',
            'password' => Hash::make('SitiPass123!'),
            'role' => 'Staff',
            'is_active' => true,
        ]);

        // Create categories
        $category1 = Category::create(['name' => 'Elektronik', 'description' => 'Perangkat elektronik']);
        $category2 = Category::create(['name' => 'Furniture', 'description' => 'Perabotan kantor']);
        $category3 = Category::create(['name' => 'Alat Tulis', 'description' => 'Alat tulis dan kertas']);

        // Create locations
        $loc1 = Location::create(['name' => 'Gudang A', 'description' => 'Gudang utama']);
        $loc2 = Location::create(['name' => 'Ruang Rapat', 'description' => 'Ruang rapat lantai 2']);
        $loc3 = Location::create(['name' => 'Perpustakaan', 'description' => 'Perpustakaan kantor']);

        // Create items
        $item1 = Item::create([
            'code' => 'PROJ-001',
            'name' => 'Proyektor',
            'category_id' => $category1->id,
            'location_id' => $loc2->id,
            'description' => 'Proyektor 3000 Lumens',
            'total_stock' => 5,
            'available_stock' => 5,
            'condition' => 'Baik',
        ]);

        $item2 = Item::create([
            'code' => 'LAP-001',
            'name' => 'Laptop',
            'category_id' => $category1->id,
            'location_id' => $loc1->id,
            'description' => 'Laptop dell untuk keperluan kerja',
            'total_stock' => 10,
            'available_stock' => 10,
            'condition' => 'Baik',
        ]);

        $item3 = Item::create([
            'code' => 'MES-001',
            'name' => 'Meja Kerja',
            'category_id' => $category2->id,
            'location_id' => $loc1->id,
            'description' => 'Meja kerja standar',
            'total_stock' => 20,
            'available_stock' => 20,
            'condition' => 'Baik',
        ]);

        $item4 = Item::create([
            'code' => 'KUR-001',
            'name' => 'Kursi Kantor',
            'category_id' => $category2->id,
            'location_id' => $loc1->id,
            'description' => 'Kursi ergonomis',
            'total_stock' => 15,
            'available_stock' => 15,
            'condition' => 'Baik',
        ]);

        $item5 = Item::create([
            'code' => 'BKU-001',
            'name' => 'Buku Referensi',
            'category_id' => $category3->id,
            'location_id' => $loc3->id,
            'description' => 'Buku referensi bisnis',
            'total_stock' => 25,
            'available_stock' => 25,
            'condition' => 'Baik',
        ]);

        // Create sample loan request
        $loan1 = Loan::create([
            'user_id' => $staff1->id,
            'status' => Loan::STATUS_PENDING,
            'purpose' => 'Presentasi proyek klien',
        ]);

        LoanItem::create([
            'loan_id' => $loan1->id,
            'item_id' => $item1->id,
            'quantity' => 1,
            'planned_return_at' => now()->addDays(3),
        ]);

        // Create approved loan
        $loan2 = Loan::create([
            'user_id' => $staff2->id,
            'status' => Loan::STATUS_BORROWED,
            'purpose' => 'Renovasi ruang kantor',
            'approved_at' => now()->subDays(5),
            'approved_by_id' => $admin->id,
        ]);

        LoanItem::create([
            'loan_id' => $loan2->id,
            'item_id' => $item3->id,
            'quantity' => 2,
            'planned_return_at' => now()->addDays(7),
        ]);

        LoanItem::create([
            'loan_id' => $loan2->id,
            'item_id' => $item4->id,
            'quantity' => 3,
            'planned_return_at' => now()->addDays(7),
        ]);

        // Decrement available stock for borrowed items
        $item3->decrement('available_stock', 2);
        $item4->decrement('available_stock', 3);
    }
}
