<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user passwords to default "password" - FOR TESTING ONLY';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting user passwords to default...');
        
        $defaultPassword = Hash::make('password');
        $count = 0;
        
        foreach (User::all() as $user) {
            $user->update(['password' => $defaultPassword]);
            $this->line("  ✓ Reset password for: {$user->email}");
            $count++;
        }
        
        $this->info("\n✓ Successfully reset passwords for {$count} users");
        $this->info("  Default password: 'password'\n");
    }
}
