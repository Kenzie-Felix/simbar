<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffLoansIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test staff can view loan requests index page
     */
    public function test_staff_can_view_loans_index(): void
    {
        // Create a staff user
        $staff = User::factory()->create(['role' => 'Staff']);

        // Create some loan records
        Loan::factory(3)->create(['user_id' => $staff->id]);

        // Login as staff
        $response = $this->actingAs($staff)->get(route('staff.loans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.index');
        $response->assertViewHas('loans');
        $response->assertViewHas('statuses');
    }

    /**
     * Test statuses array is properly formatted
     */
    public function test_statuses_array_is_correct(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));

        $statuses = $response->viewData('statuses');
        
        $this->assertIsArray($statuses);
        $this->assertCount(5, $statuses);
        $this->assertArrayHasKey('Menunggu Persetujuan', $statuses);
        $this->assertArrayHasKey('Disetujui', $statuses);
        $this->assertArrayHasKey('Ditolak', $statuses);
        $this->assertArrayHasKey('Dipinjam', $statuses);
        $this->assertArrayHasKey('Dikembalikan', $statuses);
    }

    /**
     * Test status filter works correctly
     */
    public function test_status_filter_works(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        
        // Create loans with different statuses
        Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Menunggu Persetujuan'
        ]);
        Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Disetujui'
        ]);

        // Test filtering by status
        $response = $this->actingAs($staff)->get(route('staff.loans.index', ['status' => 'Menunggu Persetujuan']));

        $response->assertStatus(200);
        $loans = $response->viewData('loans');
        $this->assertEquals(1, $loans->total());
    }

    /**
     * Test empty state when no loans exist
     */
    public function test_empty_state_shown_when_no_loans(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($staff)->get(route('staff.loans.index'));

        $response->assertStatus(200);
        $loans = $response->viewData('loans');
        $this->assertEquals(0, $loans->total());
    }

    /**
     * Test staff can only see their own loans
     */
    public function test_staff_only_sees_own_loans(): void
    {
        $staff1 = User::factory()->create(['role' => 'Staff']);
        $staff2 = User::factory()->create(['role' => 'Staff']);

        // Create loans for both staff
        Loan::factory(2)->create(['user_id' => $staff1->id]);
        Loan::factory(3)->create(['user_id' => $staff2->id]);

        // Staff1 should only see their 2 loans
        $response = $this->actingAs($staff1)->get(route('staff.loans.index'));
        
        $loans = $response->viewData('loans');
        $this->assertEquals(2, $loans->total());
    }
}
