<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Bank BCA',
                'code' => 'bca',
                'type' => 'bank',
                'description' => 'Transfer via Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Toko Mainan',
                'icon' => 'bca-logo.png',
                'instructions' => "1. Transfer ke rekening BCA 1234567890\n2. Atas nama Toko Mainan\n3. Upload bukti transfer",
                'fee' => 0,
                'fee_type' => 'fixed',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Bank Mandiri',
                'code' => 'mandiri',
                'type' => 'bank',
                'description' => 'Transfer via Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'Toko Mainan',
                'icon' => 'mandiri-logo.png',
                'instructions' => "1. Transfer ke rekening Mandiri 0987654321\n2. Atas nama Toko Mainan\n3. Upload bukti transfer",
                'fee' => 0,
                'fee_type' => 'fixed',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'GoPay',
                'code' => 'gopay',
                'type' => 'ewallet',
                'description' => 'Bayar dengan GoPay',
                'account_number' => '081234567890',
                'account_name' => 'Toko Mainan',
                'icon' => 'gopay-logo.png',
                'instructions' => "1. Transfer ke nomor GoPay 081234567890\n2. Atas nama Toko Mainan\n3. Upload bukti transfer",
                'fee' => 0,
                'fee_type' => 'fixed',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'OVO',
                'code' => 'ovo',
                'type' => 'ewallet',
                'description' => 'Bayar dengan OVO',
                'account_number' => '081234567891',
                'account_name' => 'Toko Mainan',
                'icon' => 'ovo-logo.png',
                'instructions' => "1. Transfer ke nomor OVO 081234567891\n2. Atas nama Toko Mainan\n3. Upload bukti transfer",
                'fee' => 0,
                'fee_type' => 'fixed',
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
