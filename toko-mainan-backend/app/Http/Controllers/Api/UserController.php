<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function index()
    {
        try {
            $users = User::where('role', 'customer')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'email', 'phone', 'address', 'is_blocked', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Block a user
     */
    public function blockUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent blocking admin users
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot block admin users'
                ], 403);
            }

            $user->is_blocked = true;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User blocked successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to block user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unblock a user
     */
    public function unblockUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->is_blocked = false;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User unblocked successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unblock user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user details
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
