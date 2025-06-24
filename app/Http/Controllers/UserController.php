<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $excludeRoles = $request->input('exclude_roles', []); // default to empty array

        $users = User::with('laboratory')
            ->when(!empty($excludeRoles), function ($query) use ($excludeRoles) {
                $query->whereNotIn('user_role', $excludeRoles);
            })
            ->get();

        $response = $users->map(function ($user, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $user->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $user->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = '<div class="d-flex align-items-center">' . $actionUpdate . '&nbsp;' . $actionDelete . '</div>';

            return [
                'count' => $index + 1,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'username' => $user->username,
                'user_role' => $user->user_role,
                'laboratory' => $user->laboratory ? $user->laboratory->name : 'N/A',
                'status' => $user->status ? 'Active' : 'Inactive',
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'action' => $action,
            ];
        });

        return response()->json($response);
    }
    
    public function list(Request $request): JsonResponse
    {
        $excludeRoles = $request->input('exclude_roles', []); // default to empty array

        $users = User::with('laboratory')
            ->when(!empty($excludeRoles), function ($query) use ($excludeRoles) {
                $query->whereNotIn('user_role', $excludeRoles);
            })
            ->get();

        $response = $users->map(function ($user, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $user->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $action = $actionUpdate;

            return [
                'count' => $index + 1,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'username' => $user->username,
                'user_role' => $user->user_role,
                'laboratory' => $user->laboratory ? $user->laboratory->name : 'N/A',
                'status' => $user->status ? 'Active' : 'Inactive',
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'action' => $action,
            ];
        });

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:30',
                'middle_name' => 'nullable|string|max:30',
                'last_name' => 'required|string|max:30',
                'extension_name' => 'nullable|string|max:5',
                'contact_no' => 'required|string|max:20|unique:users,contact_no',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8',
                'user_role' => 'required|in:Laboratory Head,Laboratory In-charge,Employee,Borrower,Admin',
                'laboratory_id' => 'nullable|exists:laboratories,id',
            ], [
                'first_name.required' => 'First name is required.',
                'first_name.max' => 'First name cannot exceed 30 characters.',
                'last_name.required' => 'Last name is required.',
                'last_name.max' => 'Last name cannot exceed 30 characters.',
                'contact_no.required' => 'Contact number is required.',
                'contact_no.unique' => 'This contact number is already taken.',
                'email.required' => 'Email is required.',
                'email.email' => 'Invalid email format.',
                'email.unique' => 'This email is already taken.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'user_role.required' => 'User role is required.',
                'laboratory_id.exists' => 'Selected laboratory does not exist.',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'User successfully stored.',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store user: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store user. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            DB::commit();

            return response()->json($user, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve user: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve user. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:30',
                'middle_name' => 'nullable|string|max:30',
                'last_name' => 'required|string|max:30',
                'extension_name' => 'nullable|string|max:5',
                'contact_no' => 'required|string|max:20|unique:users,contact_no,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'user_role' => 'required|in:Laboratory Head,Laboratory In-charge,Employee,Borrower,Admin',
                'laboratory_id' => 'nullable|exists:laboratories,id',
            ], [
                'first_name.required' => 'First name is required.',
                'first_name.max' => 'First name cannot exceed 30 characters.',
                'last_name.required' => 'Last name is required.',
                'last_name.max' => 'Last name cannot exceed 30 characters.',
                'contact_no.required' => 'Contact number is required.',
                'contact_no.unique' => 'This contact number is already taken.',
                'email.required' => 'Email is required.',
                'email.email' => 'Invalid email format.',
                'email.unique' => 'This email is already taken.',
                'user_role.required' => 'User role is required.',
                'laboratory_id.exists' => 'Selected laboratory does not exist.',
            ]);

            $user = User::findOrFail($id);
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
            $user->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'User successfully updated.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update user: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update user. Please try again later.',
            ], 500);
        }
    }

    public function changePassword(Request $request)
{
    // Log request method and URL (optional for debugging)
    Log::info('ChangePassword request: Method=' . $request->method() . ', URL=' . $request->fullUrl());

    // Validate the request input
    $validated = $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = auth()->user();

    // Check if the current password is correct
    if (!Hash::check($validated['current_password'], $user->password)) {
        return response()->json([
            'valid' => false,
            'msg' => 'Current password is incorrect.'
        ], 422);
    }

    // Update the password
    $user->password = Hash::make($validated['new_password']);
    $user->save();

    return response()->json([
        'valid' => true,
        'msg' => 'Password changed successfully.'
    ]);
}

public function changeUserPassword(Request $request, $id)
{
    // Validate the request input
    $validated = $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = User::findOrFail($id);

    // Check if the current password is correct
    if (!Hash::check($validated['current_password'], $user->password)) {
        return response()->json([
            'valid' => false,
            'msg' => 'Current password is incorrect.'
        ], 422);
    }

    // Update the password
    $user->password = Hash::make($validated['new_password']);
    $user->save();

    return response()->json([
        'valid' => true,
        'msg' => 'Password changed successfully.'
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'User successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete user: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete user. Please try again later.',
            ], 500);
        }
    }

    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $exists = DB::table('users')->where('username', $username)->exists();

        return response()->json(!$exists); // Only returns true or false
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = DB::table('user_profiles')->where('email', $email)->exists();

        return response()->json(!$exists); // Only returns true or false
    }

    public function checkContact(Request $request)
    {
        $contact = $request->input('contact_no');
        $exists = DB::table('user_profiles')->where('contact_no', $contact)->exists();

        return response()->json(!$exists); // Only returns true or false
    }

    public function myProfile()
    {
        try {
            $userProfile = DB::table('user_profiles')->where('id', session('id'))->first();

            if (!$userProfile) {
                return response()->json(['valid' => false, 'msg' => 'User not found'], 200);
            }

            $fullName = $this->formatFullName(
                $userProfile->first_name,
                $userProfile->middle_name,
                $userProfile->last_name,
                $userProfile->extension_name
            );

            $formattedContactNo = $this->formatContactNumber($userProfile->contact_no);

            return response()->json([
                'valid' => true,
                'msg' => 'User profile successfully retrieve',
                'fullname' => $fullName,
                'first_name' => $userProfile->first_name,
                'middle_name' => $userProfile->middle_name,
                'last_name' => $userProfile->last_name,
                'extension_name' => $userProfile->extension_name,
                'contact_no' => $formattedContactNo,
                'email' => $userProfile->email,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve profile: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to retrieve profile', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Format the full name with proper handling of the middle name.
     *
     * @param string $firstName
     * @param string|null $middleName
     * @param string $lastName
     * @param string|null $extensionName
     * @return string
     */
    private function formatFullName($firstName, $middleName, $lastName, $extensionName)
    {
        $middleInitial = $middleName ? strtoupper(substr($middleName, 0, 1)) . '.' : '';
        $fullName = trim("{$firstName} {$middleInitial} {$lastName} {$extensionName}");
        return $fullName;
    }

    /**
     * Formats a contact number from (+63) 905-747-3104 to 09057473104
     *
     * @param string $contactNo
     * @return string
     */
    private function formatContactNumber($contactNo)
    {
        // Remove spaces, parentheses, and dashes
        $cleaned = preg_replace('/[^\d]/', '', $contactNo);

        // Replace country code +63 with 0
        if (str_starts_with($cleaned, '63')) {
            $cleaned = '0' . substr($cleaned, 2);
        }

        return $cleaned;
    }
}

