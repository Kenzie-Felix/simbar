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

class LoanApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Item $item1;
    private Item $item2;
    private Category $category;
    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'Admin',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'role' => 'Staff',
            'is_active' => true,
        ]);

        // Create test category and location
        $this->category = Category::factory()->create(['name' => 'Office Supplies']);
        $this->location = Location::factory()->create(['name' => 'Storage Room A']);

        // Create test items with sufficient stock
        $this->item1 = Item::factory()->create([
            'name' => 'Test Item 1',
            'code' => 'ITEM001',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 50,
            'available_stock' => 50,
            'condition' => 'Baik',
        ]);

        $this->item2 = Item::factory()->create([
            'name' => 'Test Item 2',
            'code' => 'ITEM002',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'total_stock' => 30,
            'available_stock' => 30,
            'condition' => 'Baik',
        ]);
    }

    // ===== TEST 1: CREATE LOAN REQUEST =====

    public function test_staff_can_view_loan_creation_form()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('staff.loans.create'));

        $response->assertStatus(200);
        $response->assertViewIs('staff.loans.create');
    }

    public function test_staff_can_access_loans_index()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('staff.loans.index'));

        $response->assertStatus(200);
    }

    public function test_loan_status_starts_as_pending()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 5,
            'planned_return_at' => now()->addDays(7),
        ]);

        $this->assertEquals('Menunggu Persetujuan', $loan->status);
        $this->assertNull($loan->approved_by_id);
        $this->assertNull($loan->approved_at);
    }

    // ===== TEST 2: ADMIN APPROVE SINGLE LOAN =====

    public function test_admin_can_approve_single_loan_model_method()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 5,
            'planned_return_at' => now()->addDays(7),
        ]);

        // Test model method directly
        $loan->approve($this->admin);
        $loan->refresh();

        $this->assertEquals('Dipinjam', $loan->status);
        $this->assertEquals($this->admin->id, $loan->approved_by_id);
        $this->assertNotNull($loan->approved_at);
    }

    public function test_admin_cannot_approve_already_approved_loan()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create([
                'status' => 'Dipinjam',
                'approved_by_id' => $this->admin->id,
            ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.approve', $loan));

        $response->assertSessionHasErrors();
    }

    public function test_loan_model_approve_sets_all_fields()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->approve($this->admin);
        $loan->refresh();

        // Verify all fields are set
        $this->assertEquals('Dipinjam', $loan->status);
        $this->assertEquals($this->admin->id, $loan->approved_by_id);
        $this->assertNotNull($loan->approved_at);
        // Just verify it's a recent timestamp (within last minute)
        $this->assertTrue($loan->approved_at->greaterThan(now()->subMinute()));
    }

    public function test_cannot_approve_rejected_loan()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Ditolak']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.approve', $loan));

        $response->assertSessionHasErrors();
        $this->assertEquals('Ditolak', $loan->fresh()->status);
    }

    // ===== TEST 3: ADMIN REJECT LOAN =====

    public function test_admin_can_reject_loan()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.reject', $loan), [
                'rejection_reason' => 'Insufficient project details',
            ]);

        $loan->refresh();
        $this->assertEquals('Ditolak', $loan->status);
        $this->assertEquals('Insufficient project details', $loan->rejection_reason);
        $this->assertEquals($this->admin->id, $loan->approved_by_id);

        $response->assertRedirect();
    }

    public function test_stock_not_affected_on_rejection()
    {
        $initialStock = $this->item1->available_stock;

        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 10,
            'planned_return_at' => now()->addDays(7),
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.loans.reject', $loan), [
                'rejection_reason' => 'Insufficient stock after review',
            ]);

        $this->item1->refresh();
        $this->assertEquals($initialStock, $this->item1->available_stock);
    }

    public function test_rejection_reason_required()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.reject', $loan), [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    // ===== TEST 4: BULK APPROVE =====

    public function test_admin_can_bulk_approve_loans()
    {
        $loan1 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan1->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 5,
            'planned_return_at' => now()->addDays(7),
        ]);

        $loan2 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan2->loanItems()->create([
            'item_id' => $this->item2->id,
            'quantity' => 3,
            'planned_return_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.bulkApprove'), [
                'loan_ids' => json_encode([$loan1->id, $loan2->id]),
            ]);

        $loan1->refresh();
        $loan2->refresh();

        $this->assertEquals('Dipinjam', $loan1->status);
        $this->assertEquals('Dipinjam', $loan2->status);

        $response->assertRedirect();
    }

    public function test_bulk_approve_decreases_stock_for_all_items()
    {
        $initialStock1 = $this->item1->available_stock;
        $initialStock2 = $this->item2->available_stock;

        $loan1 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan1->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 8,
            'planned_return_at' => now()->addDays(7),
        ]);

        $loan2 = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan2->loanItems()->create([
            'item_id' => $this->item2->id,
            'quantity' => 5,
            'planned_return_at' => now()->addDays(7),
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.loans.bulkApprove'), [
                'loan_ids' => json_encode([$loan1->id, $loan2->id]),
            ]);

        $this->item1->refresh();
        $this->item2->refresh();

        $this->assertEquals($initialStock1 - 8, $this->item1->available_stock);
        $this->assertEquals($initialStock2 - 5, $this->item2->available_stock);
    }

    public function test_bulk_approve_requires_valid_json_array()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.bulkApprove'), [
                'loan_ids' => 'invalid-data',
            ]);

        $response->assertSessionHasErrors();
    }

    // ===== TEST 5: AUTHORIZATION & SECURITY =====

    public function test_staff_cannot_approve_loans()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->staff)
            ->post(route('admin.loans.approve', $loan));

        $response->assertForbidden();
    }

    public function test_staff_cannot_reject_loans()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->staff)
            ->post(route('admin.loans.reject', $loan), [
                'rejection_reason' => 'Test',
            ]);

        $response->assertForbidden();
    }

    public function test_staff_cannot_bulk_approve()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->actingAs($this->staff)
            ->post(route('admin.loans.bulkApprove'), [
                'loan_ids' => json_encode([$loan->id]),
            ]);

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $loan = Loan::factory()->create(['status' => 'Menunggu Persetujuan']);

        $response = $this->post(route('admin.loans.approve', $loan));

        $response->assertRedirect(route('login'));
    }

    // ===== TEST 6: EDGE CASES =====

    public function test_double_click_protection_on_approval()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->loanItems()->create([
            'item_id' => $this->item1->id,
            'quantity' => 10,
            'planned_return_at' => now()->addDays(7),
        ]);

        // First approve
        $loan->approve($this->admin);
        $loan->refresh();
        $this->assertEquals('Dipinjam', $loan->status);

        // Second approve attempt (should fail - already approved)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.approve', $loan));

        $response->assertSessionHasErrors();
    }

    public function test_loan_items_created_correctly()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->loanItems()->createMany([
            [
                'item_id' => $this->item1->id,
                'quantity' => 5,
                'planned_return_at' => now()->addDays(7),
            ],
            [
                'item_id' => $this->item2->id,
                'quantity' => 3,
                'planned_return_at' => now()->addDays(7),
            ],
        ]);

        $this->assertCount(2, $loan->loanItems);
        $this->assertEquals(5, $loan->loanItems[0]->quantity);
        $this->assertEquals(3, $loan->loanItems[1]->quantity);
    }

    public function test_approved_at_timestamp_set_correctly()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $loan->approve($this->admin);
        $loan->refresh();

        $this->assertNotNull($loan->approved_at);
        // Verify it's a recent timestamp (within last minute)
        $this->assertTrue($loan->approved_at->greaterThan(now()->subMinute()));
    }

    public function test_admin_id_recorded_on_approval()
    {
        $loan = Loan::factory()
            ->for($this->staff)
            ->create(['status' => 'Menunggu Persetujuan']);

        $this->actingAs($this->admin)
            ->post(route('admin.loans.approve', $loan));

        $loan->refresh();
        $this->assertEquals($this->admin->id, $loan->approved_by_id);
    }
}
