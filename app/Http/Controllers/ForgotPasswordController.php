<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset request form.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('email');
    }

    /**
     * Handle sending password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate password reset token
        $token = Str::random(64);

        // Store the password reset token in the database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => bcrypt($token),
                'created_at' => Carbon::now()
            ]
        );

        // Send password reset email
        $status = $this->sendResetEmail($request->email, $token);

        // Return response based on email sending status
        if ($status) {
            return response()->json([
                'message' => 'Password reset link sent to your email address',
                'valid' => true
            ], 200);
        }

        return response()->json([
            'message' => 'Unable to send password reset link',
            'valid' => false
        ], 500);
    }

    /**
     * Send password reset email to user.
     *
     * @param  string  $email
     * @param  string  $token
     * @return bool
     */
    protected function sendResetEmail($email, $token)
    {
        try {
            $user = User::where('email', $email)->first();

            // Prepare reset link
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $email
            ], false));

            // Send email
            Mail::send('emails.password_reset', [
                'user' => $user,
                'resetUrl' => $resetUrl
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset Request - ACC Laboratory')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }
}
