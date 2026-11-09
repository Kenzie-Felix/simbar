<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockCalculationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;
    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'Admin',
            'is_active' => true,
        ]);

        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->location = Location::factory()->create(['name' => 'Test Location']);
    }

    /**
     * Test: Increasing total_stock from 10 to 15 should increase available_stock by 5
     * Formula: available_stock + borrowed_stock = total_stock
     * 
     * Initial state:
     *   Total: 10
     *   Available: 7
     *   Borrowed: 3
     * 
     * After increase to 15:
     *   Total: 15
     *   Available: 12  (increased by 5)
     *   Borrowed: 3    (unchanged)
     */
    public function test_increasing_total_stock_increases_available_stock_by_difference(): void
    {
        // Create item with initial stock
        $item = Item::factory()->create([
            'code' => 'TEST-001',
            'name' => 'Test Item',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 7,
            'condition' => 'Baik',
        ]);

        // Verify initial state
        $this->assertEquals(10, $item->total_stock);
        $this->assertEquals(7, $item->available_stock);
        $borrowed_initially = $item->total_stock - $item->available_stock; // 3
        $this->assertEquals(3, $borrowed_initially);

        // Update total_stock from 10 to 15
        $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
            'code' => $item->code,
            'name' => $item->name,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'description' => $item->description,
            'total_stock' => 15,
            'condition' => $item->condition,
        ]);

        // Refresh item from database
        $item->refresh();

        // Verify: total_stock increased to 15
        $this->assertEquals(15, $item->total_stock);

        // Verify: available_stock increased by 5 (from 7 to 12)
        $this->assertEquals(12, $item->available_stock);

        // Verify: borrowed stock remains unchanged (3)
        $borrowed_after = $item->total_stock - $item->available_stock;
        $this->assertEquals(3, $borrowed_after);
        $this->assertEquals($borrowed_initially, $borrowed_after);
    }

    /**
     * Test: Cannot reduce total_stock below currently borrowed items
     * If 5 items are borrowed out of 10, cannot reduce total to 4
     */
    public function test_cannot_reduce_total_stock_below_borrowed_items(): void
    {
        $item = Item::factory()->create([
            'code' => 'TEST-002',
            'name' => 'Test Item 2',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 5,  // 5 borrowed
            'condition' => 'Baik',
        ]);

        // Attempt to reduce to 4 (below borrowed amount of 5)
        $response = $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
            'code' => $item->code,
            'name' => $item->name,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'description' => $item->description,
            'total_stock' => 4,  // Try to reduce below borrowed
            'condition' => $item->condition,
        ]);

        // Verify: Request failed with error
        $response->assertSessionHasErrors('total_stock');
        $this->assertStringContainsString('Cannot reduce total stock', session('errors')->first('total_stock'));

        // Verify: Stock unchanged
        $item->refresh();
        $this->assertEquals(10, $item->total_stock);
        $this->assertEquals(5, $item->available_stock);
    }

    /**
     * Test: Can reduce total_stock to exactly the borrowed amount
     * If 5 items are borrowed out of 10, can reduce to 5
     */
    public function test_can_reduce_total_stock_to_borrowed_amount(): void
    {
        $item = Item::factory()->create([
            'code' => 'TEST-003',
            'name' => 'Test Item 3',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 5,  // 5 borrowed
            'condition' => 'Baik',
        ]);

        // Reduce to exactly borrowed amount
        $response = $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
            'code' => $item->code,
            'name' => $item->name,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'description' => $item->description,
            'total_stock' => 5,  // Reduce to borrowed amount
            'condition' => $item->condition,
        ]);

        // Verify: Update successful
        $response->assertSessionHasNoErrors();

        // Verify: Stock updated correctly
        $item->refresh();
        $this->assertEquals(5, $item->total_stock);
        $this->assertEquals(0, $item->available_stock);  // All stock is borrowed
    }

    /**
     * Test: Decreasing total_stock decreases available_stock by difference
     * If total goes from 10 to 8 (decrease by 2), available should decrease by 2
     */
    public function test_decreasing_total_stock_decreases_available_stock_by_difference(): void
    {
        $item = Item::factory()->create([
            'code' => 'TEST-004',
            'name' => 'Test Item 4',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 8,  // 2 borrowed
            'condition' => 'Baik',
        ]);

        // Reduce total from 10 to 8
        $response = $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
            'code' => $item->code,
            'name' => $item->name,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'description' => $item->description,
            'total_stock' => 8,  // Reduce by 2
            'condition' => $item->condition,
        ]);

        // Verify: Update successful
        $response->assertSessionHasNoErrors();

        // Verify: Stock updated correctly
        $item->refresh();
        $this->assertEquals(8, $item->total_stock);
        $this->assertEquals(6, $item->available_stock);  // Decreased by 2
        $this->assertEquals(2, $item->total_stock - $item->available_stock);  // Borrowed unchanged
    }

    /**
     * Test: No change in total_stock means no change in available_stock
     */
    public function test_no_stock_change_preserves_available_stock(): void
    {
        $item = Item::factory()->create([
            'code' => 'TEST-005',
            'name' => 'Test Item 5',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 7,
            'condition' => 'Baik',
        ]);

        // Update without changing total_stock
        $response = $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
            'code' => 'TEST-005-NEW',  // Change code only
            'name' => 'Test Item 5 Updated',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'description' => 'Updated description',
            'total_stock' => 10,  // Same
            'condition' => $item->condition,
        ]);

        // Verify: Update successful
        $response->assertSessionHasNoErrors();

        // Verify: Stock unchanged
        $item->refresh();
        $this->assertEquals(10, $item->total_stock);
        $this->assertEquals(7, $item->available_stock);
    }

    /**
     * Test: Dashboard statistics reflect correct stock numbers
     */
    public function test_dashboard_statistics_reflect_available_stock(): void
    {
        // Create test items with different stock levels
        Item::factory()->create([
            'code' => 'ITEM-GOOD',
            'name' => 'Good Stock',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 100,
            'available_stock' => 50,  // Available
            'condition' => 'Baik',
        ]);

        Item::factory()->create([
            'code' => 'ITEM-LOW',
            'name' => 'Low Stock',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 10,
            'available_stock' => 3,  // Low
            'condition' => 'Baik',
        ]);

        Item::factory()->create([
            'code' => 'ITEM-OUT',
            'name' => 'Out of Stock',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 50,
            'available_stock' => 0,  // Out
            'condition' => 'Baik',
        ]);

        // Dashboard should show correct counts
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $stats = $response->viewData('stats');

        // Total available stock = 50 + 3 + 0 = 53
        $this->assertEquals(53, $stats['total_available_stock']);

        // Low stock items (1-5) = 1
        $this->assertEquals(1, $stats['low_stock_items']);

        // Out of stock = 1
        $this->assertEquals(1, $stats['out_of_stock_items']);
    }
}
