<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveProducts();
    public function getProductsByCategory($categoryId);
    public function searchProducts($query);
    public function updateStock($productId, $quantity);
    public function findByUuid($uuid);
}
