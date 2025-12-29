<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getActiveProducts()
    {
        return $this->model->active()->with('category')->get();
    }

    public function getProductsByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->active()
            ->with('category')
            ->get();
    }

    public function searchProducts($query)
    {
        return $this->model->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->active()
            ->with('category')
            ->get();
    }

    public function updateStock($productId, $quantity)
    {
        $product = $this->find($productId);
        $product->stock = $product->stock - $quantity;
        $product->save();
        return $product;
    }
    
    public function findByUuid($uuid)
    {
        $product = $this->model->where('uuid', $uuid)->with('category')->first();
        if (!$product) {
            throw new \Exception('Product not found');
        }
        return $product;
    }
}
