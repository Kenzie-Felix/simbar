<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class CreateUserAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new user account (Admin or Staff) with interactive password input';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        SIMBAR - CREATE USER ACCOUNT SAFELY                ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Step 1: Get user role
        $role = $this->choice(
            'Pilih role pengguna',
            ['Admin', 'Staff'],
            1 // Default to Staff
        );

        // Step 2: Get name
        $name = $this->ask('Masukkan nama lengkap pengguna');
        
        if (empty(trim($name))) {
            $this->error('❌ Nama tidak boleh kosong');
            return 1;
        }

        // Step 3: Get email
        while (true) {
            $email = $this->ask('Masukkan alamat email');
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('❌ Format email tidak valid');
                continue;
            }

            // Check if email already exists
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                $this->warn('⚠️  Email ini sudah terdaftar');
                $this->info("   Nama: {$existingUser->name}");
                $this->info("   Role: {$existingUser->role}");
                $this->info("   Status: " . ($existingUser->is_active ? 'Aktif' : 'Tidak Aktif'));
                
                if ($this->confirm('Gunakan email ini untuk mengubah password?')) {
                    // User wants to update existing user
                    return $this->updateExistingUser($existingUser);
                } else {
                    continue;
                }
            }
            
            break;
        }

        // Step 4: Get password with interactive input
        $password = $this->getPasswordInput();
        
        if ($password === false) {
            $this->error('❌ Operasi dibatalkan');
            return 1;
        }

        // Step 5: Validate password
        try {
            $this->validatePassword($password);
        } catch (ValidationException $e) {
            $this->error('❌ Password tidak memenuhi persyaratan:');
            foreach ($e->errors() as $errors) {
                foreach ($errors as $error) {
                    $this->error("   • {$error}");
                }
            }
            return 1;
        }

        // Step 6: Confirm before creating
        $this->newLine();
        $this->info('📝 Konfirmasi Data:');
        $this->info("   Nama: {$name}");
        $this->info("   Email: {$email}");
        $this->info("   Role: {$role}");
        $this->info("   Status: Aktif");
        $this->newLine();

        if (!$this->confirm('Lanjutkan membuat akun ini?')) {
            $this->error('❌ Operasi dibatalkan');
            return 1;
        }

        // Step 7: Create user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'is_active' => true,
                'department' => null,
            ]);

            $this->newLine();
            $this->info('✅ Akun berhasil dibuat!');
            $this->newLine();
            $this->line("╔════════════════════════════════════════════════════════════╗");
            $this->line("║        DETAIL AKUN YANG TELAH DIBUAT                      ║");
            $this->line("╠════════════════════════════════════════════════════════════╣");
            $this->line("║ ID: {$user->id}");
            $this->line("║ Nama: {$user->name}");
            $this->line("║ Email: {$user->email}");
            $this->line("║ Role: {$user->role}");
            $this->line("║ Status: " . ($user->is_active ? 'Aktif ✓' : 'Tidak Aktif ✗'));
            $this->line("║ Dibuat: {$user->created_at->format('d-m-Y H:i:s')}");
            $this->line("╚════════════════════════════════════════════════════════════╝");
            $this->newLine();

            // Step 8: Verify in database
            $this->info('🔍 Verifikasi di Database...');
            $verified = User::where('email', $email)->first();
            
            if ($verified) {
                $this->info('✅ Data tersimpan di database MySQL simbar');
                $this->info("   ID: {$verified->id}");
                $this->info("   Email: {$verified->email}");
                $this->info("   Role: {$verified->role}");
                $this->info("   is_active: {$verified->is_active}");
            } else {
                $this->error('❌ Verifikasi gagal - data tidak ditemukan di database');
                return 1;
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Terjadi kesalahan saat membuat akun:');
            $this->error($e->getMessage());
            return 1;
        }
    }

    /**
     * Get password input securely with confirmation
     */
    private function getPasswordInput()
    {
        $this->newLine();
        $this->info('🔐 Input Password:');
        $this->line('   Password harus memenuhi persyaratan:');
        $this->line('   • Minimal 8 karakter');
        $this->line('   • Mengandung huruf besar dan kecil');
        $this->line('   • Mengandung minimal 1 angka');
        $this->line('   • Mengandung minimal 1 simbol (!@#$%^&* dll)');
        $this->newLine();

        while (true) {
            $password = $this->secret('Masukkan password');
            
            if (empty($password)) {
                $this->error('❌ Password tidak boleh kosong');
                continue;
            }

            $confirmation = $this->secret('Konfirmasi password');
            
            if ($password !== $confirmation) {
                $this->error('❌ Password tidak cocok');
                continue;
            }

            return $password;
        }
    }

    /**
     * Validate password against Laravel Password rule
     */
    private function validatePassword($password)
    {
        $validator = validator(['password' => $password], [
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Update existing user's password (optional)
     */
    private function updateExistingUser(User $user)
    {
        $this->newLine();
        $this->info('Opsi update akun yang sudah ada:');
        
        $updateOption = $this->choice(
            'Apa yang ingin Anda lakukan?',
            [
                'Ubah password',
                'Aktifkan/Nonaktifkan akun',
                'Ubah role',
                'Batalkan',
            ],
            3 // Default cancel
        );

        switch ($updateOption) {
            case 'Ubah password':
                $this->warn('⚠️  Mengubah password akan logout semua session pengguna');
                if (!$this->confirm('Lanjutkan?')) {
                    $this->error('❌ Operasi dibatalkan');
                    return 1;
                }

                $password = $this->getPasswordInput();
                
                if ($password === false) {
                    $this->error('❌ Operasi dibatalkan');
                    return 1;
                }

                try {
                    $this->validatePassword($password);
                    $user->update(['password' => Hash::make($password)]);
                    $this->info('✅ Password berhasil diubah');
                    return 0;
                } catch (ValidationException $e) {
                    $this->error('❌ Password tidak memenuhi persyaratan');
                    return 1;
                }

            case 'Aktifkan/Nonaktifkan akun':
                $newStatus = !$user->is_active;
                $user->update(['is_active' => $newStatus]);
                $this->info('✅ Status akun diubah menjadi: ' . ($newStatus ? 'Aktif' : 'Tidak Aktif'));
                return 0;

            case 'Ubah role':
                $newRole = $this->choice('Pilih role baru', ['Admin', 'Staff'], $user->role === 'Admin' ? 0 : 1);
                $user->update(['role' => $newRole]);
                $this->info("✅ Role diubah menjadi: {$newRole}");
                return 0;

            default:
                $this->error('❌ Operasi dibatalkan');
                return 1;
        }
    }
}
