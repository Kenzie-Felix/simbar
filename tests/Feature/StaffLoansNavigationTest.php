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

class StaffLoansNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete navigation flow: index -> show -> back to index
     */
    public function test_navigation_flow_index_to_show_to_index(): void
    {
        // Setup: Create staff user and loan data
        $staff = User::factory()->create(['role' => 'Staff']);
        
        // Create category and location
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        
        // Create item
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'available_stock' => 10,
        ]);
        
        // Create a loan
        $loan = Loan::factory()->create(['user_id' => $staff->id]);
        
        // Add item to loan - create LoanItem record manually
        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'planned_return_at' => now()->addDays(7),
        ]);

        // Step 1: Access index page
        $indexResponse = $this->actingAs($staff)->get(route('staff.loans.index'));
        $this->assertEquals(200, $indexResponse->getStatusCode());
        $indexResponse->assertViewIs('staff.loans.index');
        $indexResponse->assertViewHas('loans');
        $indexResponse->assertViewHas('statuses');
        echo "✓ Step 1: Index page loads successfully\n";

        // Step 2: Click on loan to view details
        $showResponse = $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        $this->assertEquals(200, $showResponse->getStatusCode());
        $showResponse->assertViewIs('staff.loans.show');
        $showResponse->assertViewHas('loan');
        $showResponse->assertViewHas('statuses');
        echo "✓ Step 2: Show page loads successfully\n";

        // Step 3: Click "Kembali ke Daftar" button (back to index)
        $backResponse = $this->actingAs($staff)->get(route('staff.loans.index'));
        $this->assertEquals(200, $backResponse->getStatusCode());
        $backResponse->assertViewIs('staff.loans.index');
        $backResponse->assertViewHas('loans');
        $backResponse->assertViewHas('statuses');
        echo "✓ Step 3: Returning to index page works correctly\n";

        // Verify loans are still displayed
        $loansData = $backResponse->viewData('loans');
        $this->assertGreaterThan(0, $loansData->total());
        echo "✓ Step 3a: Loans data is still present after returning\n";

        // Verify statuses array is present
        $statusesData = $backResponse->viewData('statuses');
        $this->assertIsArray($statusesData);
        $this->assertCount(5, $statusesData);
        echo "✓ Step 3b: Statuses array is correct after returning\n";
    }

    /**
     * Test that back button uses correct route
     */
    public function test_back_button_uses_correct_route(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $loan = Loan::factory()->create(['user_id' => $staff->id]);

        $response = $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        
        // Check that the view contains the back button with correct route
        $response->assertSeeText('Kembali ke Daftar');
        // Verify the href points to the index route
        $this->assertStringContainsString(
            route('staff.loans.index'),
            $response->getContent()
        );
        echo "✓ Back button uses correct route\n";
    }

    /**
     * Test pagination after returning from detail
     */
    public function test_pagination_works_after_returning(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        // Create multiple loans (more than 15 to trigger pagination)
        $loans = Loan::factory(20)->create(['user_id' => $staff->id]);
        $loan = $loans->first();

        // Access detail page then return to index
        $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        $response = $this->actingAs($staff)->get(route('staff.loans.index'));

        $response->assertStatus(200);
        $loansData = $response->viewData('loans');
        $this->assertEquals(20, $loansData->total());
        $this->assertLessThanOrEqual(15, $loansData->count()); // First page should have max 15
        echo "✓ Pagination works after returning from detail\n";
    }

    /**
     * Test status filter persists through navigation
     */
    public function test_status_filter_can_be_used_after_returning(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Menunggu Persetujuan'
        ]);
        Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Disetujui'
        ]);

        // Navigate to detail and back
        $loan = $staff->loans()->first();
        $this->actingAs($staff)->get(route('staff.loans.show', $loan));
        
        // Then apply status filter
        $response = $this->actingAs($staff)->get(
            route('staff.loans.index', ['status' => 'Menunggu Persetujuan'])
        );

        $response->assertStatus(200);
        $loansData = $response->viewData('loans');
        $this->assertEquals(1, $loansData->total());
        echo "✓ Status filter works after returning from detail\n";
    }

    /**
     * Test that staff can only see their own loans after returning
     */
    public function test_staff_isolation_maintained_after_navigation(): void
    {
        $staff1 = User::factory()->create(['role' => 'Staff']);
        $staff2 = User::factory()->create(['role' => 'Staff']);
        
        Loan::factory(3)->create(['user_id' => $staff1->id]);
        Loan::factory(5)->create(['user_id' => $staff2->id]);

        // Staff1 navigates: index -> detail -> back to index
        $staff1Loans = $staff1->loans();
        $loan = $staff1Loans->first();
        
        $this->actingAs($staff1)->get(route('staff.loans.show', $loan));
        $response = $this->actingAs($staff1)->get(route('staff.loans.index'));

        $response->assertStatus(200);
        $loansData = $response->viewData('loans');
        $this->assertEquals(3, $loansData->total()); // Only staff1's 3 loans
        echo "✓ Staff data isolation maintained after returning\n";
    }
}
