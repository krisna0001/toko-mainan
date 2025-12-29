<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $shippingMethods = [
            [
                'name' => 'JNE Regular',
                'description' => 'JNE Regular service - reliable and affordable',
                'base_cost' => 15000.00,
                'estimated_days' => '3-4 hari',
                'is_active' => true,
            ],
            [
                'name' => 'JNE YES',
                'description' => 'JNE Express service - faster delivery',
                'base_cost' => 25000.00,
                'estimated_days' => '1-2 hari',
                'is_active' => true,
            ],
            [
                'name' => 'JNT Express',
                'description' => 'J&T Express delivery service',
                'base_cost' => 12000.00,
                'estimated_days' => '2-3 hari',
                'is_active' => true,
            ],
            [
                'name' => 'SiCepat REGULAR',
                'description' => 'SiCepat regular service - economical shipping',
                'base_cost' => 13000.00,
                'estimated_days' => '3-5 hari',
                'is_active' => true,
            ],
            [
                'name' => 'SiCepat BEST',
                'description' => 'SiCepat premium service - guaranteed fast delivery',
                'base_cost' => 22000.00,
                'estimated_days' => '1-2 hari',
                'is_active' => true,
            ],
            [
                'name' => 'Grab Express',
                'description' => 'Same-day delivery via Grab (only for local area)',
                'base_cost' => 30000.00,
                'estimated_days' => 'Same day',
                'is_active' => true,
            ],
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::create($method);
        }
    }
}
