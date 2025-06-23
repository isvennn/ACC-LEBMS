<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmployeeNavigationController extends Controller
{
    public function dashboard(): View
    {
        return view('employee.dashboard');
    }
    public function item(): View
    {
        return view('employee.item');
    }

    public function transaction(): View
    {
        return view('employee.transaction');
    }
}
