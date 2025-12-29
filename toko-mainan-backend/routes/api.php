<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ShippingMethodController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes - Authentication
Route::prefix('auth')->group(function () {
    // Customer registration and login (Public forms)
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1'); // 5 attempts per minute
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute
    
    // Admin secret login (Only for admin modal)
    Route::post('admin-login', [AuthController::class, 'adminLogin'])->middleware('throttle:3,1'); // 3 attempts per minute
});

// Public routes - Products & Categories (view only)
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{uuid}', [ProductController::class, 'show'])->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// Public route - Bank info for payment
Route::get('payment/bank-info', [PaymentController::class, 'getBankInfo']);

// Public route - Active shipping methods (for customer checkout)
Route::get('shipping-methods/active', [ShippingMethodController::class, 'getActive']);

// Protected routes - Requires authentication
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Admin only routes
    Route::middleware('admin')->group(function () {
        // Image upload (Admin)
        Route::post('upload-image', [ImageUploadController::class, 'upload']);
        Route::post('delete-image', [ImageUploadController::class, 'delete']);
        
        // Product management (Admin)
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
        
        // Category management (Admin)
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
        
        // Promo management (Admin)
        Route::get('promos', [PromoController::class, 'index']);
        Route::post('promos', [PromoController::class, 'store']);
        Route::get('promos/{id}', [PromoController::class, 'show']);
        Route::put('promos/{id}', [PromoController::class, 'update']);
        Route::delete('promos/{id}', [PromoController::class, 'destroy']);
        
        // Payment Methods management (Admin)
        Route::get('payment-methods', [PaymentMethodController::class, 'index']);
        Route::post('payment-methods', [PaymentMethodController::class, 'store']);
        Route::get('payment-methods/{id}', [PaymentMethodController::class, 'show']);
        Route::put('payment-methods/{id}', [PaymentMethodController::class, 'update']);
        Route::delete('payment-methods/{id}', [PaymentMethodController::class, 'destroy']);
        
        // Shipping Methods management (Admin)
        Route::get('shipping-methods', [ShippingMethodController::class, 'index']);
        Route::post('shipping-methods', [ShippingMethodController::class, 'store']);
        Route::get('shipping-methods/{id}', [ShippingMethodController::class, 'show']);
        Route::put('shipping-methods/{id}', [ShippingMethodController::class, 'update']);
        Route::delete('shipping-methods/{id}', [ShippingMethodController::class, 'destroy']);
        
        // Users management (Admin)
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::post('users/{id}/block', [UserController::class, 'blockUser']);
        Route::post('users/{id}/unblock', [UserController::class, 'unblockUser']);
        
        // Order management (Admin)
        Route::get('orders/all', [OrderController::class, 'index']);
        Route::get('orders/history', [OrderController::class, 'getAllHistory']);
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

    // Customer routes
    Route::middleware('customer')->group(function () {
        Route::post('orders/checkout', [OrderController::class, 'checkout']);
        Route::get('orders/my-orders', [OrderController::class, 'myOrders']);
        Route::get('orders/my-history', [OrderController::class, 'getHistory']);
        Route::post('orders/{id}/confirm-delivery', [OrderController::class, 'confirmDelivery']);
        
        // Payment proof upload
        Route::post('orders/{orderNumber}/upload-payment-proof', [PaymentController::class, 'uploadProof']);
    });

    // Both admin and customer can view order details
    Route::get('orders/{id}', [OrderController::class, 'show']);
    
    // Admin payment approval
    Route::middleware('admin')->group(function () {
        Route::post('orders/{orderNumber}/approve-payment', [PaymentController::class, 'approvePayment']);
        Route::post('orders/{orderNumber}/reject-payment', [PaymentController::class, 'rejectPayment']);
    });
});

// Midtrans callback (no authentication needed)
Route::post('midtrans/callback', [MidtransCallbackController::class, 'callback']);

// Health check
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});
