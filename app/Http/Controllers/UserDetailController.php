<?php

namespace App\Http\Controllers;

use App\Models\LandlordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserDetailController extends Controller
{
    use HasApiTokens, HasFactory;
    /**
     * Handle the registration of a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    

    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:user_details',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:255|unique:user_details',
            'phone_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|string|max:255',
            'id_card_picture' => 'nullable|string|max:255',
            'user_type' => 'required|in:landlord,renter',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new user
        $user = UserDetail::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'profile_picture' => $request->input('profile_picture'),
            'id_card_picture' => $request->input('id_card_picture'),
            'user_type' => $request->input('user_type'),
        ]);

        // If the user is a landlord, generate a unique join code and create a landlord detail
        if ($user->isLandlord()) {
            $joinCode = LandlordDetail::generateJoinCode(); // Generate a unique join code
            LandlordDetail::create([
                'user_id' => $user->id,
                'join_code' => $joinCode
            ]);
        }

        // Return a success response with the user details
        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user
        ], 201);
    }

    public function showUser($id)
    {
        // Find the user by id
        $user = UserDetail::with('landlordDetail')->find($id);

        // If the user does not exist, return a 404 response
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // If the user is a landlord, include the join_code in the response
        if ($user->isLandlord() && $user->landlordDetail) {
            $response = [
                'user' => $user,
                'landlord_details' => [
                    'join_code' => $user->landlordDetail->join_code
                ]
            ];
        } else {
            // If not a landlord, return only user data
            $response = [
                'user' => $user
            ];
        }

        // Return the response
        return response()->json($response, 200);
    }

    public function login(Request $request)
{
    // Validate the incoming login request
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ]);

    // Attempt to authenticate the user
    $user = UserDetail::where('email', $request->email)->first();

    // Check if user exists and password is correct
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    // If authentication passes, return user details
    $response = [
        'user' => [
            'id' => $user->id,
            'user_type' => $user->user_type,
        ]
    ];

    // If the user is a landlord, include the join_code
    if ($user->isLandlord()) {
        $response['user']['join_code'] = $user->landlordDetail->join_code ?? null;
    }

    // Return success response with user details
    return response()->json($response, 200);
}

    
}
