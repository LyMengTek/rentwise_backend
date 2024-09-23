<?php

namespace App\Http\Controllers;

use App\Models\LandlordDetail;
use App\Models\RoomDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;

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
             'message' => 'User registered successfully',
             'user' => $user,
         ], 201);
     }

     public function showUser($id)
     {
         // Find the user by ID
         $user = UserDetail::find($id);
 
         // Check if the user exists
         if (!$user) {
             return response()->json(['message' => 'User not found'], 404);
         }
 
         // Return user details
         return response()->json([
             'username' => $user->username,
             'email' => $user->email,
             'phone_number' => $user->phone_number,
             'profile_picture' => $user->profile_picture,
             'id_card_picture' => $user->id_card_picture,
             'user_type' => $user->user_type,
             'join_code' => $user->join_code,
             
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


        public function getRentersByJoinCode(Request $request): JsonResponse
        {
            // Get the currently authenticated landlord
            $landlord = UserDetail::find($request->landlord_id);
    
            // Ensure that the user is a landlord
            if (!$landlord || !$landlord->isLandlord()) {
                return response()->json(['error' => 'User is not a landlord or not found'], 403);
            }
    
            // Fetch all renters who share the landlord's join_code
            $renters = UserDetail::where('join_code', $landlord->join_code)
                                ->where('user_type', 'renter')
                                ->get();
    
            // Return the renters as a JSON response
            return response()->json($renters);
        }
        
        

        public function getAvailableRoomsByJoinCode(Request $request): JsonResponse
    {
        // Validate the join_code input
        $validator = Validator::make($request->all(), [
            'join_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the landlord based on the join_code
        $landlord = UserDetail::where('join_code', $request->join_code)
                              ->where('user_type', 'landlord')
                              ->first();

        if (!$landlord) {
            return response()->json(['error' => 'Landlord with the provided join code not found'], 404);
        }

        // Fetch available rooms that belong to this landlord
        $availableRooms = RoomDetail::where('user_id', $landlord->id)
                                    ->available() // Using the scopeAvailable method
                                    ->get();

        if ($availableRooms->isEmpty()) {
            return response()->json(['message' => 'No available rooms found for this landlord'], 200);
        }

        // Return the available rooms as a JSON response
        return response()->json($availableRooms, 200);
    }
    
}
