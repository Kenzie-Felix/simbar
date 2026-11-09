<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupInitialAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:accounts';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create initial Admin and Staff accounts (run only once for fresh setup)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        SIMBAR - INITIAL ACCOUNT SETUP                     ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Predefined accounts for initial setup
        $accounts = [
            [
                'name' => 'Administrator',
                'email' => 'admin@simbar.local',
                'role' => 'Admin',
                'password' => 'Admin@1234',
            ],
            [
                'name' => 'Staf Peminjaman',
                'email' => 'staff@simbar.local',
                'role' => 'Staff',
                'password' => 'Staff@1234',
            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($accounts as $accountData) {
            $this->line("");
            $this->info("Memproses: {$accountData['email']}");

            // Check if user already exists
            $existingUser = User::where('email', $accountData['email'])->first();

            if ($existingUser) {
                $this->warn("⚠️  Email ini sudah terdaftar (ID: {$existingUser->id})");
                $this->info("   Nama: {$existingUser->name}");
                $this->info("   Role: {$existingUser->role}");
                $this->info("   Status: " . ($existingUser->is_active ? 'Aktif' : 'Tidak Aktif'));
                $skippedCount++;
                continue;
            }

            // Create new account
            try {
                $user = User::create([
                    'name' => $accountData['name'],
                    'email' => $accountData['email'],
                    'password' => Hash::make($accountData['password']),
                    'role' => $accountData['role'],
                    'is_active' => true,
                ]);

                $this->info("✅ Akun berhasil dibuat!");
                $this->info("   ID: {$user->id}");
                $this->info("   Email: {$user->email}");
                $this->info("   Role: {$user->role}");
                $this->info("   Password: {$accountData['password']}");

                // Verify in database
                $verified = User::find($user->id);
                if ($verified) {
                    $this->info("   ✓ Terverifikasi di database MySQL simbar");
                }

                $createdCount++;

            } catch (\Exception $e) {
                $this->error("❌ Gagal membuat akun: {$e->getMessage()}");
            }
        }

        // Summary
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════════╗');
        $this->line('║                    RINGKASAN SETUP                         ║');
        $this->line('╠════════════════════════════════════════════════════════════╣');
        $this->line("║ Akun Dibuat: {$createdCount}");
        $this->line("║ Akun Dilewati: {$skippedCount}");
        $this->line('╠════════════════════════════════════════════════════════════╣');
        $this->line('║ DATA AKUN:                                                 ║');
        $this->line('║                                                            ║');
        $this->line('║ Admin:                                                     ║');
        $this->line('║   Email: admin@simbar.local                               ║');
        $this->line('║   Password: Admin@1234                                    ║');
        $this->line('║                                                            ║');
        $this->line('║ Staff:                                                     ║');
        $this->line('║   Email: staff@simbar.local                               ║');
        $this->line('║   Password: Staff@1234                                    ║');
        $this->line('║                                                            ║');
        $this->line('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Final verification
        $this->info('🔍 Verifikasi Final di Database...');
        $allUsers = User::all();
        $this->info("Total pengguna di database: {$allUsers->count()}");
        
        foreach ($allUsers as $user) {
            $this->line("   • {$user->name} ({$user->email}) - Role: {$user->role}");
        }

        if ($createdCount > 0) {
            $this->newLine();
            $this->info('✅ Setup akun berhasil!');
            $this->info('   Anda sekarang dapat login dengan akun Admin atau Staff.');
            return 0;
        } elseif ($skippedCount > 0) {
            $this->newLine();
            $this->warn('⚠️  Semua akun sudah ada (tidak ada yang dibuat)');
            return 0;
        } else {
            $this->newLine();
            $this->error('❌ Setup gagal');
            return 1;
        }
    }
}
