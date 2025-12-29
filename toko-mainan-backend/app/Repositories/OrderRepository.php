<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getOrdersByUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllOrdersForAdmin()
    {
        return $this->model
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrderByNumber($orderNumber)
    {
        return $this->model->where('order_number', $orderNumber)
            ->with('orderItems.product', 'user')
            ->firstOrFail();
    }

    public function updateStatus($orderId, $status)
    {
        $order = $this->find($orderId);
        $order->status = $status;
        $order->save();
        return $order;
    }

    public function updatePaymentStatus($orderId, $status)
    {
        $order = $this->find($orderId);
        $order->payment_status = $status;
        $order->save();
        return $order;
    }

    public function findByOrderNumber($orderNumber)
    {
        return $this->model->where('order_number', $orderNumber)->first();
    }
}
