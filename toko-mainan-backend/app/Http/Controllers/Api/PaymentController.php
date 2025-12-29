<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Upload payment proof (customer)
     */
    public function uploadProof(Request $request, $orderNumber)
    {
        try {
            \Log::info('Upload proof request received', [
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'has_file' => $request->hasFile('payment_proof')
            ]);

            $validator = Validator::make($request->all(), [
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = $this->orderRepository->findByOrderNumber($orderNumber);

            if (!$order) {
                \Log::error('Order not found', ['order_number' => $orderNumber]);
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Check if user owns this order
            if ($order->user_id !== auth()->id()) {
                \Log::error('Unauthorized access', [
                    'order_user_id' => $order->user_id,
                    'auth_user_id' => auth()->id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Delete old payment proof if exists
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            // Store new payment proof
            $file = $request->file('payment_proof');
            $filename = 'payment_proof_' . $orderNumber . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_proofs', $filename, 'public');

            \Log::info('File stored successfully', ['path' => $path]);

            // Update order
            $order->update([
                'payment_proof' => $path,
                'payment_status' => 'pending', // Changed from unpaid to pending (waiting admin confirmation)
            ]);

            \Log::info('Order updated successfully', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully',
                'data' => [
                    'payment_proof' => asset('storage/' . $path),
                    'order' => $order,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload proof exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve payment (admin)
     */
    public function approvePayment(Request $request, $orderNumber)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = $this->orderRepository->findByOrderNumber($orderNumber);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'admin_notes' => $request->admin_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment approved',
                'data' => $order,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Approval failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject payment (admin)
     */
    public function rejectPayment(Request $request, $orderNumber)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_notes' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = $this->orderRepository->findByOrderNumber($orderNumber);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->update([
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'admin_notes' => $request->admin_notes,
                'payment_proof' => null, // Clear old payment proof so user can upload new one
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected',
                'data' => $order,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods (bank, e-wallet, etc)
     */
    public function getBankInfo()
    {
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'type' => $method->type,
                    'account_number' => $method->account_number,
                    'account_name' => $method->account_name,
                    'icon' => $method->icon,
                    'instructions' => $method->instructions ? explode('\n', $method->instructions) : [],
                    'fee' => $method->fee,
                    'fee_type' => $method->fee_type,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'payment_methods' => $paymentMethods,
                'general_instructions' => [
                    'Transfer sesuai dengan total pembayaran',
                    'Upload bukti transfer setelah melakukan pembayaran',
                    'Konfirmasi pembayaran akan diproses maksimal 1x24 jam',
                    'Pesanan akan diproses setelah pembayaran dikonfirmasi',
                ]
            ]
        ]);
    }
}
