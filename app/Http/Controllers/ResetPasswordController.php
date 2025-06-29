<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset form.
     *
     * @param  string  $token
     * @param  string  $email
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle password reset submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        // Check if the token is valid
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Invalid or expired reset token',
                'valid' => false
            ], 400);
        }

        // Check if token is expired (default 60 minutes)
        $createdAt = Carbon::parse($passwordReset->created_at);
        $expireMinutes = config('auth.passwords.users.expire', 60);
        if ($createdAt->diffInMinutes(Carbon::now()) > $expireMinutes) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Password reset token has expired',
                'valid' => false
            ], 400);
        }

        try {
            // Update the user's password
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the password reset token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'message' => 'Password has been successfully reset',
                'valid' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to reset password: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unable to reset password. Please try again.',
                'valid' => false
            ], 500);
        }
    }
}
