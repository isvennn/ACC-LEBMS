<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionPenalty;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowerNavigationController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $userId = $user->id;

        // Count active transactions (Confirmed or Released)
        $activeTransactionCount = Transaction::where('user_id', $userId)
            ->whereIn('status', ['Confirmed', 'Released'])
            ->count();

        // Count pending transactions
        $pendingTransactionCount = Transaction::where('user_id', $userId)
            ->where('status', 'Pending')
            ->count();

        // Count returned items
        $returnedItemCount = Transaction::where('user_id', $userId)
            ->where('status', 'Returned')
            ->count();

        // Count overdue items (date_of_return is in the past and status is not Returned)
        $overdueItemCount = Transaction::where('user_id', $userId)
            ->where('date_of_return', '<', Carbon::today())
            ->where('status', '!=', 'Returned')
            ->count();

        // Count penalties
        $penaltyCount = TransactionPenalty::where('user_id', $userId)
            ->count();

        // Get transaction status counts
        $statusCounts = Transaction::where('user_id', $userId)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return view('borrower.dashboard', compact(
            'activeTransactionCount',
            'pendingTransactionCount',
            'returnedItemCount',
            'overdueItemCount',
            'penaltyCount',
            'statusCounts'
        ));
    }
    public function item(): View
    {
        return view('borrower.item');
    }

    public function transaction(): View
    {
        return view('borrower.transaction');
    }
}
