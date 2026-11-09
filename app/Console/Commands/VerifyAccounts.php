<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:accounts';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Verify that Admin and Staff accounts are correctly created and configured';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        SIMBAR - VERIFY ACCOUNTS                           ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $adminEmail = 'admin@simbar.local';
        $staffEmail = 'staff@simbar.local';
        
        $adminUser = User::where('email', $adminEmail)->first();
        $staffUser = User::where('email', $staffEmail)->first();

        $allValid = true;

        // Verify Admin Account
        $this->info('🔍 Verifikasi Akun Admin:');
        $this->line('─────────────────────────────────────────────────────────────');

        if (!$adminUser) {
            $this->error('❌ Akun admin tidak ditemukan');
            $allValid = false;
        } else {
            $this->info("✅ ID: {$adminUser->id}");
            $this->info("✅ Nama: {$adminUser->name}");
            $this->info("✅ Email: {$adminUser->email}");
            
            if ($adminUser->role === 'Admin') {
                $this->info("✅ Role: {$adminUser->role}");
            } else {
                $this->error("❌ Role: {$adminUser->role} (harusnya Admin)");
                $allValid = false;
            }
            
            if ($adminUser->is_active) {
                $this->info("✅ Status: Aktif");
            } else {
                $this->error("❌ Status: Tidak Aktif");
                $allValid = false;
            }
            
            if ($adminUser->isAdmin()) {
                $this->info("✅ isAdmin() method: Bekerja dengan benar");
            } else {
                $this->error("❌ isAdmin() method: Tidak bekerja");
                $allValid = false;
            }
            
            if (Hash::check('Admin@1234', $adminUser->password)) {
                $this->info("✅ Password: Terverifikasi (Admin@1234)");
            } else {
                $this->error("❌ Password: Tidak cocok dengan Admin@1234");
                $allValid = false;
            }
            
            $this->info("📅 Dibuat: {$adminUser->created_at}");
        }

        $this->newLine();

        // Verify Staff Account
        $this->info('🔍 Verifikasi Akun Staff:');
        $this->line('─────────────────────────────────────────────────────────────');

        if (!$staffUser) {
            $this->error('❌ Akun staff tidak ditemukan');
            $allValid = false;
        } else {
            $this->info("✅ ID: {$staffUser->id}");
            $this->info("✅ Nama: {$staffUser->name}");
            $this->info("✅ Email: {$staffUser->email}");
            
            if ($staffUser->role === 'Staff') {
                $this->info("✅ Role: {$staffUser->role}");
            } else {
                $this->error("❌ Role: {$staffUser->role} (harusnya Staff)");
                $allValid = false;
            }
            
            if ($staffUser->is_active) {
                $this->info("✅ Status: Aktif");
            } else {
                $this->error("❌ Status: Tidak Aktif");
                $allValid = false;
            }
            
            if ($staffUser->isStaff()) {
                $this->info("✅ isStaff() method: Bekerja dengan benar");
            } else {
                $this->error("❌ isStaff() method: Tidak bekerja");
                $allValid = false;
            }
            
            if (Hash::check('Staff@1234', $staffUser->password)) {
                $this->info("✅ Password: Terverifikasi (Staff@1234)");
            } else {
                $this->error("❌ Password: Tidak cocok dengan Staff@1234");
                $allValid = false;
            }
            
            $this->info("📅 Dibuat: {$staffUser->created_at}");
        }

        $this->newLine();

        // Database verification
        $this->info('🔍 Verifikasi Database:');
        $this->line('─────────────────────────────────────────────────────────────');

        $userCount = User::count();
        $this->info("✅ Total pengguna di database: {$userCount}");

        $adminCount = User::where('role', 'Admin')->count();
        $this->info("✅ Total Admin: {$adminCount}");

        $staffCount = User::where('role', 'Staff')->count();
        $this->info("✅ Total Staff: {$staffCount}");

        $activeCount = User::where('is_active', true)->count();
        $this->info("✅ Total Pengguna Aktif: {$activeCount}");

        $this->newLine();

        // Final status
        $this->line('╔════════════════════════════════════════════════════════════╗');
        $this->line('║                    HASIL VERIFIKASI                        ║');
        $this->line('╠════════════════════════════════════════════════════════════╣');

        if ($allValid && $adminUser && $staffUser) {
            $this->line('║ ✅ SEMUA VERIFIKASI BERHASIL!                            ║');
            $this->line('║                                                          ║');
            $this->line('║ Akun siap digunakan:                                     ║');
            $this->line('║   • admin@simbar.local (Admin)                          ║');
            $this->line('║   • staff@simbar.local (Staff)                          ║');
            $this->line('╚════════════════════════════════════════════════════════════╝');
            $this->newLine();
            $this->info('✨ Selamat! Aplikasi SIMBAR siap diakses.');
            return 0;
        } else {
            $this->line('║ ❌ BEBERAPA VERIFIKASI GAGAL                             ║');
            $this->line('╚════════════════════════════════════════════════════════════╝');
            $this->newLine();
            $this->error('⚠️  Periksa kembali akun yang dibuat.');
            return 1;
        }
    }
}
