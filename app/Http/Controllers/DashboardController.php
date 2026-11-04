<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Location;
use App\Models\BorrowReturn;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function adminDashboard()
    {
        // Cache dashboard stats for 2 hours
        $stats = Cache::remember('admin.dashboard.stats', 7200, function () {
            return [
                'total_items' => Item::count(),
                'total_categories' => Category::count(),
                'total_locations' => Location::count(),
                'total_active_loans' => Loan::where('status', Loan::STATUS_BORROWED)->count(),
                'pending_approvals' => Loan::where('status', Loan::STATUS_PENDING)->count(),
                'pending_verifications' => BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)->count(),
                'overdue_loans' => Loan::where('status', Loan::STATUS_BORROWED)
                    ->whereHas('loanItems', function ($query) {
                        $query->where('planned_return_at', '<', now())->whereNull('returned_at');
                    })
                    ->count(),
                'low_stock_items' => Item::where('available_stock', '<=', 5)
                    ->where('available_stock', '>', 0)
                    ->count(),
                'out_of_stock_items' => Item::where('available_stock', 0)->count(),
            ];
        });

        // Recent audit logs (not cached, always fresh)
        $recentLogs = AuditLog::with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
        ]);
    }

    /**
     * Show staff dashboard
     */
    public function staffDashboard()
    {
        $user = auth()->user();

        // Get user's active loans
        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', Loan::STATUS_BORROWED)
            ->with('loanItems.item')
            ->get();

        // Count overdue loans for this user
        $overdueLoanCount = 0;
        foreach ($activeLoans as $loan) {
            if ($loan->isOverdue()) {
                $overdueLoanCount++;
            }
        }

        // Get user's pending returns
        $pendingReturns = BorrowReturn::whereHas('loan', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', BorrowReturn::STATUS_PENDING)
            ->count();

        // Available items for borrowing
        $availableItemsCount = Item::where('available_stock', '>', 0)
            ->whereIn('condition', ['Baik', 'Rusak Ringan'])
            ->count();

        // Get total items borrowed (historical)
        $totalBorrowedCount = Loan::where('user_id', $user->id)->count();

        return view('staff.dashboard', [
            'activeLoans' => $activeLoans,
            'overdueLoanCount' => $overdueLoanCount,
            'pendingReturns' => $pendingReturns,
            'availableItemsCount' => $availableItemsCount,
            'totalBorrowedCount' => $totalBorrowedCount,
        ]);
    }

    /**
     * Route to appropriate dashboard based on role
     */
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->staffDashboard();
        }
    }
}
