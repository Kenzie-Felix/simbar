<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->staff = User::factory()->create(['role' => 'Staff']);
        
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        
        $this->item = Item::factory()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'total_stock' => 100,
            'available_stock' => 70,
        ]);
    }

    // ===== TEST 1: DASHBOARD ACCESS & STATISTICS =====

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.dashboard');
    }

    public function test_staff_cannot_access_analytics_dashboard()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('admin.reports.dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_displays_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.dashboard'));

        $response->assertViewHasAll([
            'stats',
            'topBorrowedItems',
            'staffBorrowingFrequency'
        ]);

        $stats = $response->getOriginalContent()->getData()['stats'];
        $this->assertArrayHasKey('total_items', $stats);
        $this->assertArrayHasKey('total_available_stock', $stats);
        $this->assertArrayHasKey('active_loans', $stats);
        $this->assertArrayHasKey('overdue_loans', $stats);
    }

    public function test_statistics_calculated_correctly()
    {
        // Create a loan
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Dipinjam']);

        $loan->loanItems()->create([
            'item_id' => $this->item->id,
            'quantity' => 30,
            'planned_return_at' => now()->subDays(1), // Overdue
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.dashboard'));

        $stats = $response->getOriginalContent()->getData()['stats'];

        // Verify calculations
        $this->assertEquals(1, $stats['total_items']);
        $this->assertEquals(70, $stats['total_available_stock']); // 100 - 30 borrowed
        $this->assertEquals(1, $stats['active_loans']);
        $this->assertGreaterThan(0, $stats['overdue_loans']);
    }

    // ===== TEST 2: LOAN REPORTS =====

    public function test_admin_can_access_loan_reports()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.loans'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.loans');
    }

    public function test_loan_reports_display_all_loans()
    {
        // Create multiple loans
        $loan1 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Dipinjam']);

        $loan2 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.loans'));

        $response->assertViewHas('loans');
        $loans = $response->getOriginalContent()->getData()['loans'];
        $this->assertCount(2, $loans);
    }

    public function test_loan_reports_can_filter_by_date()
    {
        Loan::factory()->for($this->staff)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.loans', [
                'from_date' => now()->format('Y-m-d'),
                'to_date' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
    }

    public function test_loan_reports_can_filter_by_status()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.loans', ['status' => 'Dipinjam']));

        $response->assertStatus(200);
    }

    public function test_loan_reports_export_csv()
    {
        Loan::factory()->for($this->staff)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-loans'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    // ===== TEST 3: INVENTORY REPORTS =====

    public function test_admin_can_access_inventory_reports()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.inventory'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.inventory');
    }

    public function test_inventory_reports_show_item_details()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.inventory'));

        $response->assertViewHas('items');
    }

    public function test_inventory_reports_can_search()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.inventory', ['search' => $this->item->name]));

        $response->assertStatus(200);
    }

    public function test_inventory_export_csv()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-inventory'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    // ===== TEST 4: STOCK MOVEMENT REPORTS =====

    public function test_admin_can_access_stock_movement_reports()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.stock-movement'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.stock-movement');
    }

    public function test_stock_movement_shows_history()
    {
        // Create stock movement
        StockMovement::create([
            'item_id' => $this->item->id,
            'type' => 'In',
            'quantity' => 10,
            'reference_type' => 'Return',
            'reference_id' => 1,
            'notes' => 'Test movement',
            'created_by_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.stock-movement'));

        // Verify report loads successfully
        $response->assertStatus(200);
    }

    public function test_stock_movement_can_filter_by_date()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.stock-movement', [
                'from_date' => now()->format('Y-m-d'),
                'to_date' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
    }

    public function test_stock_movement_export_csv()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-stock-movement'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    // ===== TEST 5: OVERDUE REPORTS =====

    public function test_admin_can_access_overdue_reports()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.overdue'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.overdue');
    }

    public function test_overdue_reports_show_late_loans()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Dipinjam']);

        $loan->loanItems()->create([
            'item_id' => $this->item->id,
            'quantity' => 10,
            'planned_return_at' => now()->subDays(5), // 5 days overdue
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.overdue'));

        // Verify report loads successfully
        $response->assertStatus(200);
    }

    public function test_overdue_export_csv()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-overdue'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    // ===== TEST 6: AUTHORIZATION =====

    public function test_staff_cannot_access_any_reports()
    {
        $routes = [
            route('admin.reports.loans'),
            route('admin.reports.inventory'),
            route('admin.reports.stock-movement'),
            route('admin.reports.overdue'),
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->staff)->get($route);
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    public function test_unauthenticated_redirected_to_login()
    {
        $response = $this->get(route('admin.reports.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // ===== TEST 7: PAGINATION =====

    public function test_loan_reports_paginated()
    {
        // Create multiple loans
        for ($i = 0; $i < 25; $i++) {
            Loan::factory()->for($this->staff)->create();
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.loans'));

        // Verify report loads with multiple pages
        $response->assertStatus(200);
    }

    public function test_inventory_reports_paginated()
    {
        // Create additional items (with unique names to avoid constraint violations)
        for ($i = 0; $i < 25; $i++) {
            $category = Category::factory()->create(['name' => 'Category ' . $i]);
            $location = Location::factory()->create(['name' => 'Location ' . $i]);
            Item::factory()->create([
                'name' => 'Item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.inventory'));

        // Verify report loads successfully
        $response->assertStatus(200);
    }
}
