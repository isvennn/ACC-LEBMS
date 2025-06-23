<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            // Attempt login with email or username
            $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $credentials = [$field => $credentials['login'], 'password' => $credentials['password']];

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if user is active
                if (!$user->status) {
                    Auth::logout();
                    return response()->json(['valid' => false, 'msg' => 'Your account is inactive.'], 403);
                }

                if ($user->user_role === 'Admin') {
                    return response()->json(['valid' => true, 'msg' => 'Login successful', 'redirect' => route('viewAdminDashboard')]);
                } else if ($user->user_role === 'Borrower') {
                    return response()->json(['valid' => true, 'msg' => 'Login successful', 'redirect' => route('viewBorrowerDashboard')]);
                } else if ($user->user_role === 'Employee') {
                    return response()->json(['valid' => true, 'msg' => 'Login successful', 'redirect' => route('viewEmployeeDashboard')]);
                } else if ($user->user_role === 'Laboratory Head') {
                    return response()->json(['valid' => true, 'msg' => 'Login successful', 'redirect' => route('viewStaffDashboard')]);
                } else if ($user->user_role === 'Laboratory In-charge') {
                    return response()->json(['valid' => true, 'msg' => 'Login successful', 'redirect' => route('viewStaffDashboard')]);
                }
            }

            return response()->json(['valid' => false, 'msg' => 'Invalid credentials'], 401);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'msg' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'An error occurred. Please try again.'], 500);
        }
    }

    /**
     * Display the registration view.
     */
    public function showRegisterForm(): RedirectResponse|View
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request for Borrowers.
     */
    public function register(Request $request): JsonResponse
{
    try {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:30',
            'middle_name'      => 'nullable|string|max:30',
            'last_name'        => 'required|string|max:30',
            'extension_name'   => 'nullable|string|max:5',
            'contact_no'       => 'required|string|max:20|unique:users,contact_no',
            'email'            => 'required|email|max:255|unique:users,email',
            'username'         => 'required|string|min:8|max:255|unique:users,username',
            'password'         => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[0-9]).+$/|confirmed',
        ], [
            'password.regex'    => 'Password must contain at least one uppercase letter and one number.',
            'password.confirmed'=> 'Password confirmation does not match.',
        ]);

        $user = new User();
        $user->first_name     = $validated['first_name'];
        $user->middle_name    = $validated['middle_name'] ?? null;
        $user->last_name      = $validated['last_name'];
        $user->extension_name = $validated['extension_name'] ?? null;
        $user->contact_no     = $validated['contact_no'];
        $user->email          = $validated['email'];
        $user->username       = $validated['username'];
        $user->password       = Hash::make($validated['password']);
        $user->user_role      = 'Borrower';
        $user->laboratory_id  = null;
        $user->status         = 1;
        $user->save(); // ✅ DATA IS SAVED TO DATABASE HERE

        Auth::login($user);

        return response()->json([
            'valid' => true,
            'msg' => 'Registration successful.',
            'redirect' => route('viewBorrowerDashboard') // adjust if needed
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'valid' => false,
            'msg' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Registration error: ' . $e->getMessage());
        return response()->json([
            'valid' => false,
            'msg' => 'An error occurred while saving your data.'
        ], 500);
    }
}
    /**
     * Handle logout request.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->ajax()) {
                return response()->json(['valid' => true, 'msg' => 'Logged out successfully.']);
            }

            return redirect()->route('loginPage');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to logout.'], 500);
        }
    }

    /**
     * Check if username is available.
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $exists = User::where('username', $username)->exists();
        return response()->json(!$exists);
    }

    /**
     * Check if email is available.
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $exists = User::where('email', $email)->exists();
        return response()->json(!$exists);
    }

    /**
     * Check if contact number is available.
     */
    public function checkContact(Request $request): JsonResponse
    {
        $contact = $request->input('contact_no');
        $exists = User::where('contact_no', $contact)->exists();
        return response()->json(!$exists);
    }
}
