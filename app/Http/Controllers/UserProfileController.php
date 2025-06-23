<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function myProfile(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'valid' => false,
            'error' => 'User not authenticated.'
        ], 401);
    }

    return response()->json([
        'valid' => true,
        'fullname' => $user->first_name . ' ' . $user->last_name,
        'first_name' => $user->first_name,
        'middle_name' => $user->middle_name,
        'last_name' => $user->last_name,
        'extension_name' => $user->extension_name,
        'email' => $user->email,
        'contact_no' => $user->contact_no,
    ]);
}

    public function index()
    {
        try {
            $queryPrefixes = [];

            // Check the session value and set the appropriate prefixes
            switch (session('query')) {
                case "LHD-":
                    $queryPrefixes = ["LHD-", "LIC-"]; // Include both LHD- and LID-
                    break;
                case "BRW-":
                    $queryPrefixes = ["BRW-"];
                    break;
                case "EMP-":
                    $queryPrefixes = ["EMP-"];
                    break;
                default:
                    $queryPrefixes = ["ADM-"];
            }

            // Query the database for all prefixes in $queryPrefixes
            $userProfiles = UserProfile::where(function ($query) use ($queryPrefixes) {
                foreach ($queryPrefixes as $prefix) {
                    $query->orWhere('user_id', 'LIKE', $prefix . '%');
                }
            })->get()->map(function ($list, $key) {

                $fullName = $this->formatFullName(
                    $list->first_name,
                    $list->middle_name,
                    $list->last_name,
                    $list->extension_name
                );

                $formattedContactNo = $this->formatContactNumber($list->contact_no);

                return [
                    'count' => $key + 1,
                    'user_id' => $list->user_id,
                    'fullname' => $fullName,
                    'email' => $list->email,
                    'contact_no' => $formattedContactNo,
                    'laboratory' => $list->laboratory,
                    'action' => '<button class="btn btn-primary btn-block btn-md" onclick="view(' . "'" . $list->user_id . "'" . ')" title="Edit User"><i class="fa fa-edit"></i></button>',
                ];
            });

            return response()->json(['valid' => true, 'msg' => 'User Profiles fetched successfully', 'data' => $userProfiles], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to fetch users'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:30',
                'middle_name' => 'nullable|string|max:30',
                'last_name' => 'required|string|max:30',
                'extension_name' => 'nullable|string|max:5',
                'contact_no' => 'required|string|max:20',
                'email' => 'required|email|max:50',
                'laboratory' => 'nullable|string|max:50',
                'username' => 'required|string|min:8',
                'password' => 'required|string',
                'user_role' => 'required|string',
            ]);

            $prefix = "";
            $prefixBorrower = collect(); // Default to an empty collection to prevent errors in count()

            switch ($validated['user_role']) {
                case 'Borrower':
                    $prefix = 'BRW-';
                    break;
                case 'Employee':
                    $prefix = 'EMP-';
                    break;
                case 'Laboratory Head':
                    $prefix = 'LHD-';
                    break;
                case 'Laboratory In-charge':
                    $prefix = 'LIC-';
                    break;
                default:
                    $prefix = "ADM-"; // Updated to follow the prefix structure
            }

            // Fetch records only if prefix is applicable
            if (in_array($validated['user_role'], ['Borrower', 'Employee', 'Laboratory Head', 'Laboratory In-charge'])) {
                $prefixBorrower = UserProfile::where('user_id', 'LIKE', $prefix . '%')->get();
            }

            // Generate user_id
            $user_id = $prefix . sprintf('%03d', $prefixBorrower->count() + 1);

            $user = User::create([
                'user_id' => $user_id,
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'user_role' => $validated['user_role'],
            ]);

            $userProfile = UserProfile::create([
                'user_id' => $user_id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'],
                'contact_no' => $validated['contact_no'],
                'email' => $validated['email'],
                'laboratory' => $validated['laboratory'],
            ]);

            if (!$user || !$userProfile) {
                return response()->json(['valid' => false, 'msg' => 'Failed to add user'], 500);
            }

            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'User Profile added successfully'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding user: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to add user'], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['valid' => false, 'msg' => 'User ID does not exist'], 404);
            }

            $userProfile = UserProfile::where('user_id', $user_id)->first();

            if (!$userProfile) {
                return response()->json(['valid' => false, 'msg' => 'UserProfile not found'], 404);
            }

            $validated = $request->validate([
                'first_name' => 'required|string|max:30',
                'middle_name' => 'nullable|string|max:30',
                'last_name' => 'required|string|max:30',
                'extension_name' => 'nullable|string|max:5',
                'contact_no' => 'required|string|max:20',
                'email' => 'required|email|max:50',
                'laboratory' => 'nullable|string|max:50',
            ]);

            $userProfile->update($validated);
            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'User Profile updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to update user'], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $userProfile = UserProfile::findOrFail($id);
            $userProfile->delete();
            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'User Profile deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to delete user'], 500);
        }
    }

    public function show($user_id)
    {
        try {
            $user = User::where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['valid' => false, 'msg' => 'User ID does not exist'], 404);
            }

            $userProfile = UserProfile::where('user_id', $user_id)->first();

            if (!$userProfile) {
                return response()->json(['valid' => false, 'msg' => 'User Profile not found'], 404);
            }

            return response()->json($userProfile, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to fetch user details'], 500);
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
