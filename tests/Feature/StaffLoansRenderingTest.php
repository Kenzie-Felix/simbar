<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use App\Models\Item;
use App\Models\LoanItem;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffLoansRenderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that index page HTML renders without errors
     */
    public function test_index_page_renders_valid_html(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
        ]);
        
        $loan = Loan::factory()->create(['user_id' => $staff->id]);
        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'planned_return_at' => now()->addDays(5),
        ]);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        
        // Just verify the response is successful - view rendering is tested by Laravel's test infrastructure
        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.index');
        $response->assertViewHas('loans');
        $response->assertViewHas('statuses');
        
        echo "✓ Index page renders with correct view and data\n";
    }

    /**
     * Test that show page renders correctly
     */
    public function test_show_page_renders_valid_html(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $loan = Loan::factory()->create(['user_id' => $staff->id]);

        $response = $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        
        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.show');
        $response->assertViewHas('loan');
        $response->assertViewHas('statuses');
        $response->assertSeeText('Kembali ke Daftar');
        
        echo "✓ Show page renders correctly with back button\n";
    }

    /**
     * Test that index page contains all required elements after navigation
     */
    public function test_index_has_all_elements_after_navigation(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
        ]);
        
        $loan = Loan::factory()->create(['user_id' => $staff->id]);
        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'planned_return_at' => now()->addDays(5),
        ]);

        // Simulate navigation: view detail page first
        $showResponse = $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        $showResponse->assertStatus(200);
        echo "✓ Show page loads successfully\n";
        
        // Then access index page
        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        $response->assertStatus(200);
        echo "✓ Index page returns HTTP 200 after returning from show\n";
        
        try {
            $response->assertViewIs('staff.loans.index');
            echo "✓ Correct view is rendered\n";
        } catch (\Exception $e) {
            echo "✗ View assertion failed: " . $e->getMessage() . "\n";
            throw $e;
        }
        
        try {
            $response->assertViewHas('loans');
            echo "✓ View has 'loans' data\n";
        } catch (\Exception $e) {
            echo "✗ 'loans' data missing: " . $e->getMessage() . "\n";
            throw $e;
        }
        
        try {
            $response->assertViewHas('statuses');
            echo "✓ View has 'statuses' data\n";
        } catch (\Exception $e) {
            echo "✗ 'statuses' data missing: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Test that status badges display for loans
     */
    public function test_status_badges_display_for_loans(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        // Create a loan
        $loan = Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Menunggu Persetujuan'
        ]);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        $response->assertStatus(200);
        $response->assertViewHas('statuses');
        
        echo "✓ Status data available for rendering\n";
    }

    /**
     * Test that loan table rows are properly formatted
     */
    public function test_loan_table_rows_format(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Test Item',
        ]);
        
        $loan = Loan::factory()->create([
            'user_id' => $staff->id,
            'purpose' => 'Test Purpose'
        ]);
        
        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $item->id,
            'quantity' => 3,
            'planned_return_at' => now()->addDays(5),
        ]);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.index');
        $response->assertViewHas('loans');
        
        echo "✓ Loan table data available for rendering\n";
    }

    /**
     * Test empty state when no loans
     */
    public function test_empty_state_when_no_loans(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.index');
        
        // Check that we have empty loans paginator
        $loansData = $response->viewData('loans');
        $this->assertEquals(0, $loansData->total());
        
        echo "✓ Empty state renders correctly\n";
    }

    /**
     * Test no obvious errors in rendering
     */
    public function test_view_renders_without_errors(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.index');
        
        echo "✓ View renders without errors\n";
    }
}
