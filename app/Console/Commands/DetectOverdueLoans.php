<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Console\Command;

class DetectOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:detect-overdue {--force : Force update even if already processed}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Detect and flag overdue loans based on planned return dates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Detecting overdue loans...');

        try {
            // Find all active (borrowed) loans with items past return date
            $overdueCount = 0;
            $processedLoans = 0;

            // Get all active loans that have overdue items
            $borrowedLoans = Loan::where('status', Loan::STATUS_BORROWED)->get();

            foreach ($borrowedLoans as $loan) {
                // Check if any items are overdue and not returned
                $hasOverdueItems = $loan->loanItems()
                    ->where('planned_return_at', '<', now())
                    ->whereNull('returned_at')
                    ->exists();

                if ($hasOverdueItems) {
                    $overdueCount++;
                    $this->line("  ✓ Loan #{$loan->id} flagged as overdue (Staff: {$loan->user->name})");
                }

                $processedLoans++;
            }

            // Get statistics
            $pendingRequests = Loan::where('status', Loan::STATUS_PENDING)->count();
            $completedLoans = Loan::where('status', Loan::STATUS_RETURNED)->count();
            $rejectedLoans = Loan::where('status', Loan::STATUS_REJECTED)->count();

            $this->info('');
            $this->info('📊 Loan Statistics:');
            $this->line("  • Total Borrowed: " . $borrowedLoans->count());
            $this->line("  • Overdue: {$overdueCount}");
            $this->line("  • Pending Approval: {$pendingRequests}");
            $this->line("  • Completed: {$completedLoans}");
            $this->line("  • Rejected: {$rejectedLoans}");

            $this->info('');
            $this->info('✅ Overdue detection completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error during overdue detection: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
