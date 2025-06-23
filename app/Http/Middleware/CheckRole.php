<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->user_role === 'Admin' && $request->is('admin/*')) {
                return $next($request);
            }

            if ($user->user_role === 'Employee' && $request->is('employee/*')) {
                return $next($request);
            }

            if ($user->user_role === 'Borrower' && $request->is('borrower/*')) {
                return $next($request);
            }

            if (($user->user_role === 'Laboratory Head' || $user->user_role === 'Laboratory In-charge') && $request->is('staff/*')) {
                return $next($request);
            }

            if ($user->user_role === 'Admin') {
                return redirect()->route('viewAdminDashboard');
            } else if ($user->user_role === 'Borrower') {
                return redirect()->route('viewBorrowerDashboard');
            } else if ($user->user_role === 'Employee') {
                return redirect()->route('viewEmployeeDashboard');
            } else if ($user->user_role === 'Laboratory Head') {
                return redirect()->route('viewStaffDashboard');
            } else if ($user->user_role === 'Laboratory In-charge') {
                return redirect()->route('viewStaffDashboard');
            }
        }

        // If not authenticated or user_role is not valid, redirect to login
        return redirect()->route('loginPage'); // Update with your login route name
    }
}
