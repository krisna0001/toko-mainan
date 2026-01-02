<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @method static \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard auth()
 */
class AuthController extends Controller
{
    /**
     * Register new user (Customer Only)
     * Admin registration is not allowed through public form
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // SECURITY: Check if email contains admin patterns
            if (preg_match('/@admin\.|admin@|admin-/i', $request->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email format'
                ], 422);
            }

            DB::beginTransaction();
            
            // SECURITY: Force customer role, ignore any role input
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer', // Always customer, never accept role from input
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            DB::commit();

            $token = auth()->login($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user (Customer Only - Public Form)
     * Admin cannot login through this endpoint
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            // Attempt authentication
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = auth()->user();

            // SECURITY: Check if user is blocked
            if ($user->is_blocked) {
                auth()->logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut.'
                ], 403);
            }

            // SECURITY: Block admin from logging in through customer form
            if ($user->role === 'admin') {
                auth()->logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin Login (Secret Form Only)
     * Only accessible through admin secret modal
     */
    public function adminLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            // Attempt authentication
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid admin credentials'
                ], 401);
            }

            $user = auth()->user();

            // SECURITY: Check if user is blocked
            if ($user->is_blocked) {
                auth()->logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut.'
                ], 403);
            }

            // SECURITY: Only allow admin role
            if ($user->role !== 'admin') {
                auth()->logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access only.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()
        ]);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'token' => auth()->refresh(),
                'token_type' => 'bearer',
            ]
        ]);
    }
}
