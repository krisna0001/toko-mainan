<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    protected $orderRepository;
    protected $productRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        try {
            $user = auth()->user();

            if ($user->isAdmin()) {
                $orders = $this->orderRepository->getAllOrdersForAdmin();
            } else {
                $orders = $this->orderRepository->getOrdersByUser($user->id);
            }

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myOrders()
    {
        try {
            $orders = $this->orderRepository->getOrdersByUser(auth()->id());
            
            // Log for debugging
            Log::info('My Orders Response', [
                'user_id' => auth()->id(),
                'orders_count' => $orders->count(),
                'sample_admin_notes' => $orders->first() ? $orders->first()->admin_notes : null
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        try {
            Log::info('Checkout Request', ['data' => $request->all()]);
            
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_address' => 'required|string',
                'phone' => 'required|string',
                'shipping_method_id' => 'required|exists:shipping_methods,id',
                'shipping_latitude' => 'nullable|numeric',
                'shipping_longitude' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::error('Validation Failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Get shipping method
            $shippingMethod = \App\Models\ShippingMethod::findOrFail($request->shipping_method_id);
            Log::info('Shipping Method', ['method' => $shippingMethod]);

            $totalAmount = 0;
            $orderItemsData = [];
            $itemDetails = [];

            foreach ($request->items as $item) {
                $product = $this->productRepository->find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Product {$product->name} out of stock");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                // Prepare for Midtrans
                $itemDetails[] = [
                    'id' => $product->id,
                    'price' => (int) $product->price,
                    'quantity' => $item['quantity'],
                    'name' => substr($product->name, 0, 50),
                ];
            }

            // Generate unique order number
            $orderNumber = 'ORD-' . date('YmdHis') . '-' . auth()->id();

            // Use shipping method base cost
            $shippingCost = $shippingMethod->base_cost;
            $grandTotal = $totalAmount + $shippingCost;

            Log::info('Creating Order', [
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal
            ]);

            $order = $this->orderRepository->create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal,
                'shipping_method_id' => $request->shipping_method_id,
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->phone,
                'shipping_latitude' => $request->shipping_latitude,
                'shipping_longitude' => $request->shipping_longitude,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => 'manual_transfer',
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
                $this->productRepository->updateStock($itemData['product_id'], $itemData['quantity']);
            }

            // Configure Midtrans
            $snapToken = null;
            $serverKey = config('services.midtrans.server_key');
            
            // Only attempt Midtrans if credentials are configured
            if ($serverKey && $serverKey !== 'your-server-key-here') {
                Config::$serverKey = $serverKey;
                Config::$isProduction = config('services.midtrans.is_production', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $user = auth()->user();

                // Prepare transaction data for Midtrans
                $transactionDetails = [
                    'order_id' => $orderNumber,
                    'gross_amount' => (int) $grandTotal,
                ];

                $customerDetails = [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->phone,
                    'shipping_address' => [
                        'address' => $request->shipping_address,
                    ],
                ];

                // Add shipping cost to item details
                $itemDetails[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $shippingCost,
                    'quantity' => 1,
                    'name' => 'Shipping Cost',
                ];

                $transactionData = [
                    'transaction_details' => $transactionDetails,
                    'item_details' => $itemDetails,
                    'customer_details' => $customerDetails,
                ];

                try {
                    $snapToken = Snap::getSnapToken($transactionData);
                    $order->update(['snap_token' => $snapToken]);
                } catch (\Exception $e) {
                    Log::error('Midtrans Error: ' . $e->getMessage());
                    // Continue without Midtrans if it fails
                    $snapToken = null;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order->load('orderItems.product'),
                    'snap_token' => $snapToken,
                    'order_number' => $orderNumber,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->orderRepository->find($id);
            $user = auth()->user();

            if (!$user->isAdmin() && $order->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load('orderItems.product', 'user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,processing,paid,shipped,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $order = $this->orderRepository->updateStatus($id, $request->status);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirmDelivery($id)
    {
        try {
            $order = Order::with(['orderItems.product', 'shippingMethod', 'user'])->findOrFail($id);

            // Check if user owns this order
            if ($order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Check payment status first
            if ($order->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order must be paid first. Current payment status: ' . $order->payment_status
                ], 400);
            }

            // Check if order is in valid status for confirmation
            $validStatuses = ['processing', 'shipped'];
            if (!in_array($order->status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be confirmed. Current status: ' . $order->status . '. Valid statuses: ' . implode(', ', $validStatuses)
                ], 400);
            }

            DB::beginTransaction();
            
            $order->status = 'completed';
            $order->save();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => $order->fresh(['orderItems.product', 'shippingMethod', 'user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Confirm delivery error: ' . $e->getMessage(), [
                'order_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory()
    {
        try {
            $userId = auth()->id();
            
            $orders = Order::where('user_id', $userId)
                ->whereIn('status', ['completed', 'cancelled'])
                ->with(['orderItems.product', 'shippingMethod'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllHistory()
    {
        try {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $orders = Order::whereIn('status', ['completed', 'cancelled'])
                ->with(['orderItems.product', 'user', 'shippingMethod'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function midtransCallback(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Midtrans callback received (not implemented yet)'
        ]);
    }

    public function store(Request $request)
    {
        return $this->checkout($request);
    }

    public function update(Request $request, string $id)
    {
        return $this->updateStatus($request, $id);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Delete order not allowed'
        ], 403);
    }
}
