<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function callback(Request $request)
    {
        try {
            // Configure Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production', false);

            // Get notification object
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $orderNumber = $notification->order_id;

            Log::info('Midtrans Callback Received', [
                'order_number' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            // Find order by order number
            $order = $this->orderRepository->findByOrderNumber($orderNumber);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Update order based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $order->update([
                        'status' => 'processing',
                        'payment_status' => 'paid',
                        'payment_type' => $notification->payment_type,
                        'midtrans_transaction_id' => $notification->transaction_id,
                    ]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $order->update([
                    'status' => 'processing',
                    'payment_status' => 'paid',
                    'payment_type' => $notification->payment_type,
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            } elseif ($transactionStatus == 'pending') {
                $order->update([
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_type' => $notification->payment_type,
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            } elseif ($transactionStatus == 'deny') {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'payment_type' => $notification->payment_type,
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            } elseif ($transactionStatus == 'expire') {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'payment_type' => $notification->payment_type,
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            } elseif ($transactionStatus == 'cancel') {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'payment_type' => $notification->payment_type,
                    'midtrans_transaction_id' => $notification->transaction_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Callback processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
