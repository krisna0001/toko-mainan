<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('total_amount');
            $table->decimal('grand_total', 10, 2)->after('shipping_cost');
            $table->string('shipping_phone')->nullable()->after('shipping_address');
            $table->decimal('shipping_latitude', 10, 8)->nullable()->after('shipping_phone');
            $table->decimal('shipping_longitude', 11, 8)->nullable()->after('shipping_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'grand_total', 'shipping_phone', 'shipping_latitude', 'shipping_longitude']);
        });
    }
};
