<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StaffNavigationController extends Controller
{
    public function dashboard(): View
    {
        return view('staff.dashboard');
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

    public function report(): View
    {
        $users = User::whereIn('user_role', ['Borrower', 'Employee'])->get();
        return view('staff.report', compact('users'));
    }
}
