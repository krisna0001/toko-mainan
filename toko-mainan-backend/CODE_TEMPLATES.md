# 📝 Template & Code Snippets

Gunakan template ini untuk mempercepat development.

## Controller Template

### Product Controller - CRUD Complete

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->middleware('auth:api');
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        try {
            $query = $request->get('q');
            $categoryId = $request->get('category_id');

            if ($query) {
                $products = $this->productRepository->searchProducts($query);
            } elseif ($categoryId) {
                $products = $this->productRepository->getProductsByCategory($categoryId);
            } else {
                $products = $this->productRepository->getActiveProducts();
            }

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $product = $this->productRepository->create($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productRepository->find($id);
            return response()->json([
                'success' => true,
                'data' => $product->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'sometimes|exists:categories,id',
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $product = $this->productRepository->update($id, $request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->productRepository->delete($id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### Category Controller Template

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index()
    {
        try {
            $categories = $this->categoryRepository->all();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $category = $this->categoryRepository->create($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryRepository->find($id);
            return response()->json([
                'success' => true,
                'data' => $category->load('products')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $category = $this->categoryRepository->update($id, $request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->categoryRepository->delete($id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### Order Controller Template (dengan Midtrans)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderRepository;
    protected $productRepository;
    protected $midtransService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        MidtransService $midtransService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->midtransService = $midtransService;
        $this->middleware('auth:api');
    }

    public function index()
    {
        try {
            $user = auth()->user();

            if ($user->isAdmin()) {
                $orders = $this->orderRepository->paginate(20);
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

    public function checkout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_address' => 'required|string',
                'shipping_phone' => 'required|string',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Calculate total & create order
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = $this->productRepository->find($item['product_id']);

                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Product {$product->name} out of stock");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Create order
            $order = $this->orderRepository->create([
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
                // Update stock
                $this->productRepository->updateStock($item['product_id'], $item['quantity']);
            }

            // Create Midtrans transaction
            $snapToken = $this->midtransService->createTransaction($order, $orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order->load('orderItems.product'),
                    'snap_token' => $snapToken,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
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

            // Check authorization
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
            // Admin only
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

    public function midtransCallback(Request $request)
    {
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            if ($hashed !== $request->signature_key) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 403);
            }

            $order = $this->orderRepository->getOrderByNumber($request->order_id);

            DB::beginTransaction();

            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $this->orderRepository->updatePaymentStatus($order->id, 'paid');
                $this->orderRepository->updateStatus($order->id, 'processing');
            } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                $this->orderRepository->updatePaymentStatus($order->id, 'failed');
                $this->orderRepository->updateStatus($order->id, 'cancelled');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Callback processed'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Callback failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

## Midtrans Service Template

```php
<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction($order, $items)
    {
        $itemDetails = [];
        foreach ($items as $item) {
            $itemDetails[] = [
                'id' => $item['product_id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['product']->name ?? 'Product',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_phone,
            ],
            'item_details' => $itemDetails,
        ];

        $snapToken = Snap::getSnapToken($params);
        return $snapToken;
    }

    public function getTransactionStatus($orderId)
    {
        return Transaction::status($orderId);
    }
}
```

## Seeder Templates

### Admin User Seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tokomainan.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '08123456789',
            'address' => 'Jakarta, Indonesia',
        ]);

        User::create([
            'name' => 'Customer Test',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'phone' => '08987654321',
            'address' => 'Bandung, Indonesia',
        ]);
    }
}
```

### Category Seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Action Figures', 'description' => 'Koleksi action figures dan superhero'],
            ['name' => 'Boneka', 'description' => 'Boneka dan plushies lucu'],
            ['name' => 'Puzzle & Board Games', 'description' => 'Puzzle dan permainan papan'],
            ['name' => 'Mobil-mobilan', 'description' => 'Koleksi toy cars dan vehicles'],
            ['name' => 'Mainan Edukatif', 'description' => 'Mainan untuk belajar dan berkembang'],
            ['name' => 'Lego & Building Blocks', 'description' => 'Set lego dan building blocks'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

### Product Seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Spider-Man Action Figure',
                'description' => 'Action figure Spider-Man 12 inch dengan artikulasi lengkap',
                'price' => 250000,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Batman Action Figure',
                'description' => 'Action figure Batman dengan aksesoris lengkap',
                'price' => 275000,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Teddy Bear Giant',
                'description' => 'Boneka teddy bear ukuran besar (60cm)',
                'price' => 350000,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Hot Wheels Racing Car Set',
                'description' => 'Set 5 mobil hot wheels racing dengan track',
                'price' => 180000,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Lego City Police Station',
                'description' => 'Set lego kantor polisi dengan 400+ pieces',
                'price' => 450000,
                'stock' => 25,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
```

## Copy-paste files ini ke location yang sesuai untuk mempercepat development!
