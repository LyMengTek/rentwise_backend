<?php

namespace App\Http\Controllers;

use App\Models\LandlordDetail;
use App\Models\RoomDetail;
use Illuminate\Http\Request;
use App\Models\RentalDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserDetailController extends Controller
{
    use HasApiTokens, HasFactory;
    /**
     * Handle the registration of a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function register(Request $request): JsonResponse
    {
        try {
            // Validate the request input
            $request->validate([
                'username' => 'required|string|max:255|unique:user_details',
                'email' => 'required|email|max:255|unique:user_details',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|string|max:15',
                'user_type' => 'required|in:landlord,renter',
                'profile_picture' => 'nullable|string',
                'id_card_picture' => 'nullable|string',
            ]);

            // Prepare data for new user
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'profile_picture' => $request->profile_picture,
                'id_card_picture' => $request->id_card_picture,
                'user_type' => $request->user_type,
            ];

            // Generate join_code for landlords or set it to null for renters
            if ($request->user_type === 'landlord') {
                $data['join_code'] = UserDetail::generateJoinCode();  // Generate unique 5-digit join code
            } else {
                $data['join_code'] = null;  // Set join_code to null for renters
            }

            // Create the new user
            $user = UserDetail::create($data);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => $user,
            ], 201);
        } catch (ValidationException $e) {
            // Return a validation error response
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function showUser($id)
    {
        // Find the user by ID
        $user = UserDetail::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        // Load rentals with invoices
        $rentals = RentalDetail::where('landlord_id', $user->id)
            ->with('invoices')
            ->get();

        // Return user details
        return response()->json([
            'status' => 'success',
            'message' => 'User details retrieved successfully',
            'data' => [
                'username' => $user->username,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'profile_picture' => $user->profile_picture,
                'id_card_picture' => $user->id_card_picture,
                'user_type' => $user->user_type,
                'join_code' => $user->join_code,
                'rentals' => $rentals,
            ]
        ]);
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
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        // Generate a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Prepare the response
        $response = [
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'user_type' => $user->user_type,
                'username' => $user->username,
                'token' => $token, // Include the token in the response
            ]
        ];

        // If the user is a landlord, include the join_code and available room count
        if ($user->isLandlord()) {
            $response['user']['join_code'] = $user->join_code;

            // Get available rooms count
            $availableRoomCount = RoomDetail::where('user_id', $user->id)
                ->available() // Using the scopeAvailable method
                ->count();

            $response['user']['available_rooms'] = $availableRoomCount;
        }

        // Return success response with user details
        return response()->json($response, 200);
    }
}
