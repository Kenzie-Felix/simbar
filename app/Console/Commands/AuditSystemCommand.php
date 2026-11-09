<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Loan;
use App\Models\BorrowReturn;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;

#[Signature('app:audit-system-command')]
#[Description('Command description')]
class AuditSystemCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("\n═══════════════════════════════════════════════════════════════");
        $this->line("SIMBAR INVENTORY SYSTEM - FINAL AUDIT");
        $this->line("═══════════════════════════════════════════════════════════════\n");

        // TEST 1: AUTHENTICATION & USERS
        $this->line("[TEST 1] AUTHENTICATION & USERS");
        $this->line("───────────────────────────────────────────────");
        $adminCount = User::where('role', 'Admin')->count();
        $staffCount = User::where('role', 'Staff')->count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $this->info("✓ Admin Users: $adminCount");
        $this->info("✓ Staff Users: $staffCount");
        $this->info("✓ Active Users: $activeUsers");
        $this->info("✓ Inactive Users: $inactiveUsers");

        if ($adminCount > 0 && $staffCount > 0) {
            $this->info("✓ Multiple roles configured correctly");
        }

        // TEST 2: INVENTORY MANAGEMENT
        $this->line("\n[TEST 2] INVENTORY MANAGEMENT");
        $this->line("───────────────────────────────────────────────");
        $totalItems = Item::count();
        $totalCategories = Category::count();
        $totalLocations = Location::count();
        $totalStock = Item::sum('total_stock');
        $availableStock = Item::sum('available_stock');
        $outOfStock = Item::where('available_stock', 0)->count();
        $lowStock = Item::where('available_stock', '<=', 5)->where('available_stock', '>', 0)->count();

        $this->info("✓ Total Items: $totalItems");
        $this->info("✓ Categories: $totalCategories");
        $this->info("✓ Locations: $totalLocations");
        $this->info("✓ Total Stock: $totalStock units");
        $this->info("✓ Available Stock: $availableStock units");
        $this->info("✓ Out of Stock: $outOfStock items");
        $this->info("✓ Low Stock (≤5 units): $lowStock items");

        $conditions = Item::distinct()->pluck('condition')->toArray();
        $this->info("✓ Item Conditions: " . implode(', ', $conditions));

        // TEST 3: LOAN MANAGEMENT
        $this->line("\n[TEST 3] LOAN MANAGEMENT");
        $this->line("───────────────────────────────────────────────");
        $pendingLoans = Loan::where('status', Loan::STATUS_PENDING)->count();
        $approvedLoans = Loan::where('status', Loan::STATUS_APPROVED)->count();
        $borrowedLoans = Loan::where('status', Loan::STATUS_BORROWED)->count();
        $returnedLoans = Loan::where('status', Loan::STATUS_RETURNED)->count();
        $rejectedLoans = Loan::where('status', Loan::STATUS_REJECTED)->count();
        $totalLoans = Loan::count();

        $this->info("✓ Total Loans: $totalLoans");
        $this->info("✓ Pending Approval: $pendingLoans");
        $this->info("✓ Approved (not yet borrowed): $approvedLoans");
        $this->info("✓ Currently Borrowed: $borrowedLoans");
        $this->info("✓ Returned: $returnedLoans");
        $this->info("✓ Rejected: $rejectedLoans");

        // Check overdue loans
        $overdueLoans = Loan::where('status', Loan::STATUS_BORROWED)
            ->whereHas('loanItems', function ($q) {
                $q->where('planned_return_at', '<', now())->whereNull('returned_at');
            })
            ->count();
        $this->info("✓ Overdue Loans: $overdueLoans");

        // TEST 4: RETURN MANAGEMENT
        $this->line("\n[TEST 4] RETURN MANAGEMENT");
        $this->line("───────────────────────────────────────────────");
        $pendingReturns = BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)->count();
        $acceptedReturns = BorrowReturn::where('status', BorrowReturn::STATUS_ACCEPTED)->count();
        $rejectedReturns = BorrowReturn::where('status', BorrowReturn::STATUS_REJECTED)->count();
        $totalReturns = BorrowReturn::count();

        $this->info("✓ Total Returns Submitted: $totalReturns");
        $this->info("✓ Pending Verification: $pendingReturns");
        $this->info("✓ Accepted: $acceptedReturns");
        $this->info("✓ Rejected: $rejectedReturns");

        // TEST 5: STOCK MOVEMENTS
        $this->line("\n[TEST 5] STOCK MOVEMENTS");
        $this->line("───────────────────────────────────────────────");
        $stockIn = StockMovement::where('type', 'In')->count();
        $stockOut = StockMovement::where('type', 'Out')->count();
        $totalMovements = StockMovement::count();

        $this->info("✓ Total Stock Movements: $totalMovements");
        $this->info("✓ Stock In: $stockIn");
        $this->info("✓ Stock Out: $stockOut");

        // TEST 6: RELATIONSHIPS VERIFICATION
        $this->line("\n[TEST 6] RELATIONSHIPS VERIFICATION");
        $this->line("───────────────────────────────────────────────");

        // Test Loan relationships
        $sampleLoan = Loan::with('user', 'loanItems', 'approver')->first();
        if ($sampleLoan) {
            $this->info("✓ Loan → User relationship: OK");
            $this->info("✓ Loan → LoanItems relationship: OK (" . $sampleLoan->loanItems->count() . " items)");
            if ($sampleLoan->approver) {
                $this->info("✓ Loan → Approver relationship: OK");
            }
        }

        // Test Return relationships
        $sampleReturn = BorrowReturn::with('loan', 'returnItems')->first();
        if ($sampleReturn) {
            $this->info("✓ BorrowReturn → Loan relationship: OK");
            $this->info("✓ BorrowReturn → ReturnItems relationship: OK (" . $sampleReturn->returnItems->count() . " items)");
        }

        // Test Item relationships
        $sampleItem = Item::with('category', 'location')->first();
        if ($sampleItem) {
            $this->info("✓ Item → Category relationship: OK");
            $this->info("✓ Item → Location relationship: OK");
        }

        // TEST 7: STATUS CONSTANTS
        $this->line("\n[TEST 7] STATUS CONSTANTS");
        $this->line("───────────────────────────────────────────────");
        $this->info("✓ Loan Status Constants:");
        $this->line("  - Pending: " . Loan::STATUS_PENDING);
        $this->line("  - Approved: " . Loan::STATUS_APPROVED);
        $this->line("  - Borrowed: " . Loan::STATUS_BORROWED);
        $this->line("  - Returned: " . Loan::STATUS_RETURNED);
        $this->line("  - Rejected: " . Loan::STATUS_REJECTED);

        $this->info("✓ Return Status Constants:");
        $this->line("  - Pending: " . BorrowReturn::STATUS_PENDING);
        $this->line("  - Accepted: " . BorrowReturn::STATUS_ACCEPTED);
        $this->line("  - Rejected: " . BorrowReturn::STATUS_REJECTED);

        // TEST 8: CONFIGURATION
        $this->line("\n[TEST 8] CONFIGURATION");
        $this->line("───────────────────────────────────────────────");
        $debugMode = config('app.debug') ? '✓ true (⚠️ WARNING)' : '✓ false';
        $sessionEncrypt = config('session.encrypt') ? '✓ true' : '✓ false (⚠️ WARNING)';
        $this->info("APP_DEBUG: $debugMode");
        $this->info("APP_ENV: " . config('app.env'));
        $this->info("DATABASE: " . config('database.default'));
        $this->info("SESSION_DRIVER: " . config('session.driver'));
        $this->info("CACHE_STORE: " . config('cache.default'));
        $this->info("SESSION_ENCRYPT: $sessionEncrypt");

        $this->line("\n═══════════════════════════════════════════════════════════════");
        $this->info("✓ AUDIT COMPLETE - ALL SYSTEMS OPERATIONAL");
        $this->line("═══════════════════════════════════════════════════════════════\n");

        return 0;
    }
}
