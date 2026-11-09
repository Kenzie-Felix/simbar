<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DiagnoseRoleIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:role-issue';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Diagnose the 403 Staff access required issue for Admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        SIMBAR - ROLE ISSUE DIAGNOSTIC                      ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Step 1: Check Admin account in database
        $this->info('🔍 Step 1: Check Admin Account in Database');
        $this->line('─────────────────────────────────────────────────────────────');

        $admin = User::where('email', 'admin@simbar.local')->first();

        if (!$admin) {
            $this->error('❌ Admin account not found in database');
            return 1;
        }

        $this->info("✅ Admin account found:");
        $this->info("   ID: {$admin->id}");
        $this->info("   Name: {$admin->name}");
        $this->info("   Email: {$admin->email}");
        $this->info("   Role (raw): '{$admin->role}'");
        $this->info("   Role length: " . strlen($admin->role));
        $this->info("   Role bytes: " . bin2hex($admin->role));
        $this->info("   is_active: {$admin->is_active}");
        $this->info("   Password match Admin@1234: " . (Hash::check('Admin@1234', $admin->password) ? 'true' : 'false'));

        // Step 2: Test isAdmin() method
        $this->newLine();
        $this->info('🔍 Step 2: Test isAdmin() Method');
        $this->line('─────────────────────────────────────────────────────────────');

        $isAdmin = $admin->isAdmin();
        $this->info("isAdmin() returns: " . ($isAdmin ? 'true' : 'false'));

        // Step 3: Direct comparison
        $this->newLine();
        $this->info('🔍 Step 3: Direct Role Comparison');
        $this->line('─────────────────────────────────────────────────────────────');

        $this->info("admin->role === 'Admin': " . ($admin->role === 'Admin' ? 'true' : 'false'));
        $this->info("admin->role == 'Admin': " . ($admin->role == 'Admin' ? 'true' : 'false'));
        $this->info("admin->role === 'Staff': " . ($admin->role === 'Staff' ? 'true' : 'false'));
        $this->info("strtolower(admin->role): '{$admin->role}' vs 'admin'");

        // Step 4: Check Staff account
        $this->newLine();
        $this->info('🔍 Step 4: Check Staff Account');
        $this->line('─────────────────────────────────────────────────────────────');

        $staff = User::where('email', 'staff@simbar.local')->first();

        if (!$staff) {
            $this->error('❌ Staff account not found in database');
        } else {
            $this->info("✅ Staff account found:");
            $this->info("   ID: {$staff->id}");
            $this->info("   Name: {$staff->name}");
            $this->info("   Email: {$staff->email}");
            $this->info("   Role (raw): '{$staff->role}'");
            $this->info("   Role length: " . strlen($staff->role));
            $this->info("   is_active: {$staff->is_active}");
            $this->info("   isStaff() returns: " . ($staff->isStaff() ? 'true' : 'false'));
        }

        // Step 5: Simulate authentication
        $this->newLine();
        $this->info('🔍 Step 5: Simulate Authentication Flow');
        $this->line('─────────────────────────────────────────────────────────────');

        // Try to authenticate as admin
        $credentials = [
            'email' => 'admin@simbar.local',
            'password' => 'Admin@1234',
        ];

        if (Auth::attempt($credentials)) {
            $this->info("✅ Auth::attempt() succeeded");
            $authenticatedUser = Auth::user();
            $this->info("   Authenticated User ID: {$authenticatedUser->id}");
            $this->info("   Authenticated User Email: {$authenticatedUser->email}");
            $this->info("   Authenticated User Role: '{$authenticatedUser->role}'");
            $this->info("   isAdmin(): " . ($authenticatedUser->isAdmin() ? 'true' : 'false'));
            $this->info("   isStaff(): " . ($authenticatedUser->isStaff() ? 'true' : 'false'));

            Auth::logout();
            $this->info("✓ Logged out");
        } else {
            $this->error("❌ Auth::attempt() failed");
        }

        // Step 6: Check middleware
        $this->newLine();
        $this->info('🔍 Step 6: Middleware Registration');
        $this->line('─────────────────────────────────────────────────────────────');

        $this->info("EnsureAdminRole::class exists: " . (class_exists(\App\Http\Middleware\EnsureAdminRole::class) ? 'true' : 'false'));
        $this->info("EnsureStaffRole::class exists: " . (class_exists(\App\Http\Middleware\EnsureStaffRole::class) ? 'true' : 'false'));

        // Step 7: Summary
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════════╗');
        $this->line('║                    DIAGNOSTIC SUMMARY                      ║');
        $this->line('╠════════════════════════════════════════════════════════════╣');

        if ($admin && $admin->isAdmin() && Hash::check('Admin@1234', $admin->password)) {
            $this->line('║ ✅ ADMIN ACCOUNT: Configured correctly                  ║');
        } else {
            $this->line('║ ❌ ADMIN ACCOUNT: Problem detected                      ║');
        }

        if ($staff && $staff->isStaff() && Hash::check('Staff@1234', $staff->password)) {
            $this->line('║ ✅ STAFF ACCOUNT: Configured correctly                  ║');
        } else {
            $this->line('║ ❌ STAFF ACCOUNT: Problem detected                      ║');
        }

        $this->line('╚════════════════════════════════════════════════════════════╝');

        // Final recommendation
        $this->newLine();
        if ($admin->isAdmin() && $staff->isStaff()) {
            $this->info('💡 Recommendation: Check routes/middleware configuration.');
            $this->info('   The issue may be in how routes are configured.');
            return 0;
        } else {
            $this->error('❌ Role configuration issue detected in database or model.');
            return 1;
        }
    }
}
