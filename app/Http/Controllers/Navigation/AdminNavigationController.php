<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Laboratory;
use App\Models\Transaction;
use App\Models\TransactionPenalty;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNavigationController extends Controller
{

    public function dashboard(Request $request): View|JsonResponse
    {
        // Stock Summary (Bar Chart)
        $stockQuery = Item::join('categories', 'items.category_id', '=', 'categories.id')
            ->join('laboratories', 'categories.laboratory_id', '=', 'laboratories.id')
            ->selectRaw('laboratories.name as laboratory_name, SUM(items.current_qty) as total_qty')
            ->groupBy('laboratories.name');

        if ($request->laboratory_id) {
            $stockQuery->where('laboratories.id', $request->laboratory_id);
        }

        $stockSummary = $stockQuery->get();

        // Transaction Statuses (Pie Chart)
        $statusQuery = Transaction::selectRaw('status, COUNT(*) as count')
            ->groupBy('status');

        if ($request->laboratory_id) {
            $statusQuery->whereHas('item.category', function ($q) use ($request) {
                $q->where('laboratory_id', $request->laboratory_id);
            });
        }

        $transactionStatuses = $statusQuery->pluck('count', 'status')->toArray();

        // Category Type Distribution (Pie Chart)
        $categoryTypeQuery = Item::join('categories', 'items.category_id', '=', 'categories.id')
            ->selectRaw('categories.category_type, COUNT(items.id) as count')
            ->groupBy('categories.category_type');

        if ($request->laboratory_id) {
            $categoryTypeQuery->where('categories.laboratory_id', $request->laboratory_id);
        }

        $categoryTypes = $categoryTypeQuery->pluck('count', 'category_type')->toArray();

        // Borrower Activity (Bar Chart)
        $borrowerActivityQuery = Transaction::with('user')
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5);

        if ($request->laboratory_id) {
            $borrowerActivityQuery->whereHas('item.category', function ($q) use ($request) {
                $q->where('laboratory_id', $request->laboratory_id);
            });
        }

        $borrowerActivity = $borrowerActivityQuery->get()->map(function ($item) {
            return [
                'full_name' => $item->user ? $item->user->full_name : 'Unknown',
                'count' => $item->count
            ];
        });

        // Penalty Trends (Line Chart)
        $penaltyTrendsQuery = TransactionPenalty::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month');

        if ($request->laboratory_id) {
            $penaltyTrendsQuery->whereHas('item.category', function ($q) use ($request) {
                $q->where('laboratory_id', $request->laboratory_id);
            });
        }

        $penaltyTrends = $penaltyTrendsQuery->pluck('total', 'month')->toArray();

        // Overdue Transactions by Laboratory (Bar Chart)
        $overdueQuery = Transaction::join('items', 'transactions.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->join('laboratories', 'categories.laboratory_id', '=', 'laboratories.id')
            ->selectRaw('laboratories.name as laboratory_name, COUNT(*) as count')
            ->where('date_of_return', '<', now())
            ->whereNotIn('status', ['Returned', 'Cancelled'])
            ->groupBy('laboratories.name');

        if ($request->laboratory_id) {
            $overdueQuery->where('laboratories.id', $request->laboratory_id);
        }

        $overdueTransactions = $overdueQuery->pluck('count', 'laboratory_name')->toArray();

        // Widget Counts
        $laboratoryCount = Laboratory::count();
        $categoryCount = $request->laboratory_id
            ? Category::where('laboratory_id', $request->laboratory_id)->count()
            : Category::count();
        $itemCount = $request->laboratory_id
            ? Item::whereHas('category', function ($q) use ($request) {
                $q->where('laboratory_id', $request->laboratory_id);
            })->count()
            : Item::count();
        $borrowerCount = $request->laboratory_id
            ? User::where('user_role', 'Borrower')->where('laboratory_id', $request->laboratory_id)->count()
            : User::where('user_role', 'Borrower')->count();
        $employeeCount = $request->laboratory_id
            ? User::where('user_role', 'Employee')->where('laboratory_id', $request->laboratory_id)->count()
            : User::where('user_role', 'Employee')->count();
        $transactionCount = $request->laboratory_id
            ? Transaction::whereHas('item.category', function ($q) use ($request) {
                $q->where('laboratory_id', $request->laboratory_id);
            })->count()
            : Transaction::count();

        // Laboratories for filter
        $laboratories = Laboratory::all();

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'stockSummary' => $stockSummary,
                'transactionStatuses' => $transactionStatuses,
                'categoryTypes' => $categoryTypes,
                'borrowerActivity' => $borrowerActivity,
                'penaltyTrends' => $penaltyTrends,
                'overdueTransactions' => $overdueTransactions,
                'laboratoryCount' => $laboratoryCount,
                'categoryCount' => $categoryCount,
                'itemCount' => $itemCount,
                'borrowerCount' => $borrowerCount,
                'employeeCount' => $employeeCount,
                'transactionCount' => $transactionCount,
            ]);
        }

        return view('admin.dashboard', compact(
            'stockSummary',
            'transactionStatuses',
            'categoryTypes',
            'borrowerActivity',
            'penaltyTrends',
            'overdueTransactions',
            'laboratoryCount',
            'categoryCount',
            'itemCount',
            'borrowerCount',
            'employeeCount',
            'transactionCount',
            'laboratories'
        ));
    }

    public function laboratory(): View
    {
        return view('admin.laboratory');
    }

    public function staff(): View
    {
        return view('admin.staff');
    }

    public function employee(): View
    {
        return view('admin.employee');
    }

    public function borrower(): View
    {
        return view('admin.borrower');
    }

    public function category(): View
    {
        return view('admin.category');
    }

    public function item(): View
    {
        return view('admin.item');
    }

    public function transaction(): View
    {
        return view('admin.transaction');
    }

    public function inventory(): View
    {
        return view('admin.inventory');
    }

    public function penalty(): View
    {
        return view('admin.penalty');
    }

    public function report(): View
    {
        $laboratories = Laboratory::all();
        $users = User::whereIn('user_role', ['Borrower', 'Employee'])->get();
        return view('admin.report', compact('laboratories', 'users'));
    }

    public function user(): View
    {
        return view('admin.user');
    }
}
