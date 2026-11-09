<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create test users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@simbar.local',
            'password' => bcrypt('Admin@1234'),
            'role' => 'Admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@simbar.local',
            'password' => bcrypt('Staff@1234'),
            'role' => 'Staff',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_admin_dashboard_route(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_admin_cannot_access_staff_dashboard_route(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/staff/dashboard');
        $response->assertStatus(403);
    }

    public function test_generic_dashboard_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_staff_can_access_staff_dashboard_route(): void
    {
        $staff = User::where('email', 'staff@simbar.local')->first();
        $this->actingAs($staff);

        $response = $this->get('/staff/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('staff.dashboard');
    }

    public function test_staff_cannot_access_admin_dashboard_route(): void
    {
        $staff = User::where('email', 'staff@simbar.local')->first();
        $this->actingAs($staff);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_generic_dashboard_redirects_staff_to_staff_dashboard(): void
    {
        $staff = User::where('email', 'staff@simbar.local')->first();
        $this->actingAs($staff);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/staff/dashboard');
    }

    public function test_admin_can_access_admin_items(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/items');
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_items(): void
    {
        $staff = User::where('email', 'staff@simbar.local')->first();
        $this->actingAs($staff);

        $response = $this->get('/admin/items');
        $response->assertStatus(403);
    }

    public function test_staff_can_access_staff_items(): void
    {
        $staff = User::where('email', 'staff@simbar.local')->first();
        $this->actingAs($staff);

        $response = $this->get('/staff/items');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_staff_items(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/staff/items');
        $response->assertStatus(403);
    }

    public function test_role_based_access_control_comprehensive(): void
    {
        $admin = User::where('email', 'admin@simbar.local')->first();
        $staff = User::where('email', 'staff@simbar.local')->first();

        // Admin can access admin routes
        $this->actingAs($admin);
        $this->get('/admin/dashboard')->assertStatus(200);
        $this->get('/admin/loans')->assertStatus(200);
        $this->get('/admin/returns')->assertStatus(200);

        // Admin cannot access staff routes
        $this->get('/staff/dashboard')->assertStatus(403);
        $this->get('/staff/items')->assertStatus(403);
        $this->get('/staff/loans')->assertStatus(403);

        // Staff can access staff routes
        $this->actingAs($staff);
        $this->get('/staff/dashboard')->assertStatus(200);
        $this->get('/staff/items')->assertStatus(200);
        $this->get('/staff/loans')->assertStatus(200);

        // Staff cannot access admin routes
        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/admin/items')->assertStatus(403);
        $this->get('/admin/loans')->assertStatus(403);
    }
}
