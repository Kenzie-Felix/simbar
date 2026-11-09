<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('diagnose:login')]
#[Description('Diagnose SIMBAR login issues')]
class DiagnoseLogin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("\n=== SIMBAR LOGIN DIAGNOSTIC REPORT ===\n");

        // Check database connection
        try {
            $db = app('db');
            $conn = $db->connection();
            $dbName = $conn->getDatabaseName();
            $this->line("Database Connection: ✓");
            $this->line("  Database Name: $dbName");
            $this->line("  Driver: " . $conn->getDriverName());
            $this->line("");
        } catch (\Exception $e) {
            $this->error("Database Connection: ✗");
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        // Check admin user
        $this->line("=== ADMIN USER CHECK ===");
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $this->line("Admin user found: YES");
            $this->line("  Email: admin@example.com");
            $this->line("  Name: " . $admin->name);
            $this->line("  Role: " . $admin->role);
            $this->line("  Active: " . ($admin->is_active ? 'YES' : 'NO'));
            
            // Test password
            $testPassword = 'password';
            $passwordMatch = Hash::check($testPassword, $admin->password);
            $this->line("  Password 'password' matches: " . ($passwordMatch ? 'PASS' : 'FAIL'));
        } else {
            $this->warn("Admin user found: NO");
        }

        $this->line("");

        // Check staff user
        $this->line("=== STAFF USER CHECK ===");
        $staff = User::where('email', 'staff@example.com')->first();
        if ($staff) {
            $this->line("Staff user found: YES");
            $this->line("  Email: staff@example.com");
            $this->line("  Name: " . $staff->name);
            $this->line("  Role: " . $staff->role);
            $this->line("  Active: " . ($staff->is_active ? 'YES' : 'NO'));
            
            // Test password
            $testPassword = 'password';
            $passwordMatch = Hash::check($testPassword, $staff->password);
            $this->line("  Password 'password' matches: " . ($passwordMatch ? 'PASS' : 'FAIL'));
        } else {
            $this->warn("Staff user found: NO");
        }

        $this->line("");

        // Check total users
        $totalUsers = User::count();
        $this->line("=== TOTAL USERS ===");
        $this->line("Total users in database: $totalUsers");

        $this->line("\n=== END DIAGNOSTIC ===\n");
        
        return 0;
    }
}
