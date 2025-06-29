<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffNavigationController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $laboratoryId = $user->laboratory_id;

        // Count categories for the user's laboratory
        $categoryCount = Category::where('laboratory_id', $laboratoryId)->count();

        // Count items for the user's laboratory
        $itemCount = Item::whereHas('category', function ($query) use ($laboratoryId) {
            $query->where('laboratory_id', $laboratoryId);
        })->count();

        // Count borrowers (users with Borrower role) in the laboratory
        $borrowerCount = User::where('laboratory_id', $laboratoryId)
            ->where('user_role', 'Borrower')
            ->count();

        // Count transactions for the laboratory
        $transactionCount = Transaction::whereHas('item.category', function ($query) use ($laboratoryId) {
            $query->where('laboratory_id', $laboratoryId);
        })->count();

        // Get item condition counts
        $conditionCounts = TransactionStatus::whereHas('item.category', function ($query) use ($laboratoryId) {
            $query->where('laboratory_id', $laboratoryId);
        })
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return view('staff.dashboard', compact(
            'categoryCount',
            'itemCount',
            'borrowerCount',
            'transactionCount',
            'conditionCounts'
        ));
    }

    public function category(): View
    {
        return view('staff.category');
    }

    public function item(): View
    {
        return view('staff.item');
    }

    public function transaction(): View
    {
        return view('staff.transaction');
    }

    public function inventory(): View
    {
        return view('staff.inventory');
    }

    public function penalties(): View
    {
        return view('staff.penalty');
    }

    public function report(): View
    {
        $users = User::whereIn('user_role', ['Borrower', 'Employee'])->get();
        return view('staff.report', compact('users'));
    }
}
