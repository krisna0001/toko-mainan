<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Spider-Man Marvel Legends Action Figure',
                'description' => 'Action figure Spider-Man 12 inch dengan 20+ artikulasi, aksesoris web shooter, dan stand display. Material high quality PVC.',
                'price' => 450000,
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1608889335941-32ac5f2041b9?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Batman Dark Knight Figure',
                'description' => 'Action figure Batman dengan cape, batarang, dan grappling hook. Tinggi 30cm dengan detail sempurna.',
                'price' => 525000,
                'stock' => 18,
                'image' => 'https://images.unsplash.com/photo-1608889476561-6242cfdbf622?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Iron Man Mark 85 Deluxe',
                'description' => 'Figure Iron Man Endgame dengan LED light di chest dan eyes. Include multiple hands dan effects parts.',
                'price' => 650000,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1608889825103-eb5ed706fc64?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Teddy Bear Giant Premium',
                'description' => 'Boneka teddy bear ukuran jumbo (80cm) dengan bulu super lembut, mata kristal, dan pita satin. Perfect untuk kado.',
                'price' => 485000,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1551361415-69c87624334f?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Unicorn Rainbow Plushie',
                'description' => 'Boneka unicorn dengan rambut rainbow glitter, sayap sparkle, dan ekspresi lucu. Size 50cm.',
                'price' => 275000,
                'stock' => 32,
                'image' => 'https://images.unsplash.com/photo-1563379091339-03b47348c1aa?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Bunny Soft Toy Collection',
                'description' => 'Set 3 boneka kelinci dengan warna pastel (pink, blue, lavender). Super soft dan hypoallergenic.',
                'price' => 320000,
                'stock' => 28,
                'image' => 'https://images.unsplash.com/photo-1530595467537-0b5996c41f2d?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Puzzle Sunset Beach 1000pcs',
                'description' => 'Puzzle landscape pemandangan pantai sunset dengan kualitas gambar HD. Include poster dan glue.',
                'price' => 165000,
                'stock' => 45,
                'image' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Monopoly Classic Board Game',
                'description' => 'Monopoly edisi klasik dengan token metal, uang monopoli baru, dan box premium. 2-6 players.',
                'price' => 385000,
                'stock' => 35,
                'image' => 'https://images.unsplash.com/photo-1611371805429-8b5c1b2c34ba?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Hot Wheels Track Set Ultimate',
                'description' => 'Set lengkap 10 mobil Hot Wheels dengan mega track loop, launcher, dan obstacle. Include storage box.',
                'price' => 425000,
                'stock' => 22,
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'RC Drift Car Professional',
                'description' => 'Mobil RC drift speed tinggi dengan remote 2.4GHz, rechargeable battery, dan ban drift khusus. Scale 1:16.',
                'price' => 685000,
                'stock' => 14,
                'image' => 'https://images.unsplash.com/photo-1544991875-5ac1f8e793fd?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Die Cast Car Collection Ferrari',
                'description' => 'Set koleksi 5 mobil Ferrari die cast metal dengan detail interior dan eksterior. Scale 1:43.',
                'price' => 550000,
                'stock' => 19,
                'image' => 'https://images.unsplash.com/photo-1583267746897-d2fe155088f0?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 5,
                'name' => 'Montessori Learning Kit Deluxe',
                'description' => 'Set lengkap mainan edukatif Montessori: alphabet, numbers, shapes, colors. Include panduan orang tua.',
                'price' => 575000,
                'stock' => 24,
                'image' => 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 5,
                'name' => 'STEM Robot Building Kit',
                'description' => 'Kit robot edukatif dengan 200+ parts, motor, sensor, dan buku instruksi. Bisa dibuat 8 model berbeda.',
                'price' => 725000,
                'stock' => 16,
                'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Lego City Fire Station Ultimate',
                'description' => 'Set Lego stasiun pemadam kebakaran dengan truck, helikopter, dan 6 minifigures. Total 850+ pieces.',
                'price' => 1250000,
                'stock' => 8,
                'image' => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Lego Creator 3-in-1 Dragon',
                'description' => 'Lego Creator bisa dibuat jadi dragon, phoenix, atau scorpion. 234 pieces dengan detail amazing.',
                'price' => 385000,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1614359402484-63c7e24f6d8f?w=400',
                'is_active' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Building Blocks Mega City 500pcs',
                'description' => 'Building blocks kompatibel Lego dengan tema kota modern. Include gedung, kendaraan, dan figures.',
                'price' => 295000,
                'stock' => 42,
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'is_active' => $product['is_active'],
            ]);
        }
    }
}
