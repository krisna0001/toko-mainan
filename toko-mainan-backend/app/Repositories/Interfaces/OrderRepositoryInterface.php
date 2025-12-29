<?php

namespace App\Repositories\Interfaces;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrdersByUser($userId);
    public function getAllOrdersForAdmin();
    public function getOrderByNumber($orderNumber);
    public function updateStatus($orderId, $status);
    public function updatePaymentStatus($orderId, $status);
}
