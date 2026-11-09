<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\BorrowReturn;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * Display reporting dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_items' => Item::count(),
            'total_available_stock' => Item::sum('available_stock'),
            'total_stock_value' => Item::sum('total_stock'),
            'active_loans' => Loan::where('status', Loan::STATUS_BORROWED)->count(),
            'pending_requests' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'completed_loans' => Loan::where('status', Loan::STATUS_RETURNED)->count(),
            'rejected_loans' => Loan::where('status', Loan::STATUS_REJECTED)->count(),
            'overdue_loans' => $this->getOverdueLoanCount(),
            'pending_verifications' => BorrowReturn::where('status', BorrowReturn::STATUS_PENDING)->count(),
            'accepted_returns' => BorrowReturn::where('status', BorrowReturn::STATUS_ACCEPTED)->count(),
            'rejected_returns' => BorrowReturn::where('status', BorrowReturn::STATUS_REJECTED)->count(),
            'low_stock_items' => Item::where('available_stock', '<=', 5)
                ->where('available_stock', '>', 0)
                ->count(),
            'out_of_stock_items' => Item::where('available_stock', 0)->count(),
            'damaged_items' => Item::where('condition', 'Rusak Berat')->count(),
            'lost_items' => Item::where('condition', 'Hilang')->count(),
        ];

        // Get top borrowed items
        $topBorrowedItems = $this->getTopBorrowedItems(5);

        // Get staff borrowing frequency
        $staffBorrowingFrequency = $this->getStaffBorrowingFrequency(5);

        return view('admin.reports.dashboard', compact('stats', 'topBorrowedItems', 'staffBorrowingFrequency'));
    }

    /**
     * Display loan reports
     */
    public function loans(Request $request)
    {
        $query = Loan::with('user', 'approver', 'loanItems.item');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('requested_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('requested_at', '<=', $request->get('date_to'));
        }

        // Filter by staff member
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $loans = $query->latest('requested_at')->paginate(25);

        $statuses = [
            Loan::STATUS_PENDING => 'Pending',
            Loan::STATUS_APPROVED => 'Approved',
            Loan::STATUS_BORROWED => 'Borrowed',
            Loan::STATUS_RETURNED => 'Returned',
            Loan::STATUS_REJECTED => 'Rejected',
        ];

        return view('admin.reports.loans', compact('loans', 'statuses'));
    }

    /**
     * Display inventory reports
     */
    public function inventory(Request $request)
    {
        $query = Item::with('category', 'location', 'loanItems');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->get('location_id'));
        }

        // Filter by stock level
        if ($request->filled('stock_level')) {
            $level = $request->get('stock_level');
            if ($level === 'low') {
                $query->where('available_stock', '<=', 5)->where('available_stock', '>', 0);
            } elseif ($level === 'out') {
                $query->where('available_stock', 0);
            } elseif ($level === 'good') {
                $query->where('available_stock', '>', 5);
            }
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->get('condition'));
        }

        $items = $query->paginate(25);

        return view('admin.reports.inventory', compact('items'));
    }

    /**
     * Display stock movement history
     */
    public function stockMovement(Request $request)
    {
        $query = StockMovement::with('item', 'createdBy');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->get('item_id'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by reference type
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->get('reference_type'));
        }

        $movements = $query->latest('created_at')->paginate(50);

        return view('admin.reports.stock-movement', compact('movements'));
    }

    /**
     * Display overdue loans
     */
    public function overdue(Request $request)
    {
        // Get overdue loans
        $query = Loan::where('status', Loan::STATUS_BORROWED)
            ->whereHas('loanItems', function ($q) {
                $q->where('planned_return_at', '<', now())->whereNull('returned_at');
            })
            ->with('user', 'loanItems.item');

        // Filter by days overdue
        if ($request->filled('days_overdue')) {
            $days = (int) $request->get('days_overdue');
            $query->whereHas('loanItems', function ($q) use ($days) {
                $q->where('planned_return_at', '<=', now()->subDays($days));
            });
        }

        // Filter by staff member
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Search by staff name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $overdueLoans = $query->latest('requested_at')->paginate(25);

        return view('admin.reports.overdue', compact('overdueLoans'));
    }

    /**
     * Export loan report to CSV
     */
    public function exportLoans(Request $request)
    {
        $query = Loan::with('user', 'loanItems.item');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('requested_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('requested_at', '<=', $request->get('date_to'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $loans = $query->get();

        $csv = "Loan ID,Staff Name,Staff Email,Status,Items Count,Total Quantity,Requested Date,Approved Date\n";

        foreach ($loans as $loan) {
            $csv .= "{$loan->id},";
            $csv .= "\"{$loan->user->name}\",";
            $csv .= "{$loan->user->email},";
            $csv .= "{$loan->status},";
            $csv .= "{$loan->getTotalItemsCount()},";
            $csv .= "{$loan->getTotalQuantity()},";
            $csv .= "{$loan->requested_at->format('Y-m-d H:i')},";
            $csv .= ($loan->approved_at ? $loan->approved_at->format('Y-m-d H:i') : '') . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="loan_report_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export inventory report to CSV
     */
    public function exportInventory(Request $request)
    {
        $query = Item::with('category', 'location');

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->get('location_id'));
        }

        $items = $query->get();

        $csv = "Item Code,Item Name,Category,Location,Total Stock,Available Stock,Condition,Status\n";

        foreach ($items as $item) {
            $status = 'Normal';
            if ($item->available_stock === 0) {
                $status = 'Out of Stock';
            } elseif ($item->available_stock <= 5) {
                $status = 'Low Stock';
            }

            $csv .= "{$item->code},";
            $csv .= "\"{$item->name}\",";
            $csv .= "\"{$item->category->name}\",";
            $csv .= "\"{$item->location->name}\",";
            $csv .= "{$item->total_stock},";
            $csv .= "{$item->available_stock},";
            $csv .= "{$item->condition},";
            $csv .= "{$status}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="inventory_report_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export stock movement report to CSV
     */
    public function exportStockMovement(Request $request)
    {
        $query = StockMovement::with('item', 'createdBy');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $movements = $query->get();

        $csv = "Date,Item Code,Item Name,Type,Quantity,Reference,Notes,Created By\n";

        foreach ($movements as $movement) {
            $csv .= "{$movement->created_at->format('Y-m-d H:i')},";
            $csv .= "{$movement->item->code},";
            $csv .= "\"{$movement->item->name}\",";
            $csv .= "{$movement->type},";
            $csv .= "{$movement->quantity},";
            $csv .= "\"" . ($movement->getReference() ?? '') . "\",";
            $csv .= "\"" . ($movement->notes ?? '') . "\",";
            $csv .= "\"{$movement->createdBy->name}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="stock_movement_report_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export overdue report to CSV
     */
    public function exportOverdue()
    {
        $overdueLoans = Loan::where('status', Loan::STATUS_BORROWED)
            ->whereHas('loanItems', function ($q) {
                $q->where('planned_return_at', '<', now())->whereNull('returned_at');
            })
            ->with('user', 'loanItems.item')
            ->get();

        $csv = "Loan ID,Staff Name,Staff Email,Item,Quantity,Due Date,Days Overdue\n";

        foreach ($overdueLoans as $loan) {
            foreach ($loan->loanItems as $loanItem) {
                if ($loanItem->planned_return_at < now() && is_null($loanItem->returned_at)) {
                    // Calculate days overdue correctly (positive number)
                    $daysOverdue = max(0, (int) $loanItem->planned_return_at->diffInDays(now()));
                    $csv .= "{$loan->id},";
                    $csv .= "\"{$loan->user->name}\",";
                    $csv .= "{$loan->user->email},";
                    $csv .= "\"{$loanItem->item->name}\",";
                    $csv .= "{$loanItem->quantity},";
                    $csv .= "{$loanItem->planned_return_at->format('Y-m-d')},";
                    $csv .= "{$daysOverdue}\n";
                }
            }
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="overdue_report_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * API endpoint for staff search with autocomplete
     */
    public function apiStaffSearch(Request $request)
    {
        $query = $request->input('q', '');
        $limit = 10;

        $staffMembers = \App\Models\User::where('role', 'Staff')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%");
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'results' => $staffMembers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => "{$user->name} ({$user->email})",
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }),
        ]);
    }

    /**
     * Helper: Get count of overdue loans
     */
    private function getOverdueLoanCount(): int
    {
        return Loan::where('status', Loan::STATUS_BORROWED)
            ->whereHas('loanItems', function ($q) {
                $q->where('planned_return_at', '<', now())->whereNull('returned_at');
            })
            ->count();
    }

    /**
     * Helper: Get top borrowed items
     */
    private function getTopBorrowedItems(int $limit = 5): array
    {
        return LoanItem::selectRaw('item_id, SUM(quantity) as total_borrowed')
            ->groupBy('item_id')
            ->orderByDesc('total_borrowed')
            ->limit($limit)
            ->with('item')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name,
                    'code' => $item->item->code,
                    'total_borrowed' => $item->total_borrowed,
                ];
            })
            ->toArray();
    }

    /**
     * Helper: Get staff borrowing frequency
     */
    private function getStaffBorrowingFrequency(int $limit = 5): array
    {
        return Loan::selectRaw('user_id, COUNT(*) as total_loans')
            ->groupBy('user_id')
            ->orderByDesc('total_loans')
            ->limit($limit)
            ->with('user')
            ->get()
            ->map(function ($loan) {
                return [
                    'name' => $loan->user->name,
                    'email' => $loan->user->email,
                    'total_loans' => $loan->total_loans,
                ];
            })
            ->toArray();
    }
}
