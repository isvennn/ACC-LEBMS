<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BorrowerNavigationController extends Controller
{
    public function dashboard(): View
    {
        return view('borrower.dashboard');
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
