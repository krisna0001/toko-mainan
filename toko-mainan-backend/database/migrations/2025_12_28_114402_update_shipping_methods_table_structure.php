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
        Schema::table('shipping_methods', function (Blueprint $table) {
            // Drop old columns if exist
            if (Schema::hasColumn('shipping_methods', 'code')) {
                $table->dropColumn(['code', 'icon', 'cost', 'cost_type', 'order']);
            }
            
            // Add new columns if not exist
            if (!Schema::hasColumn('shipping_methods', 'base_cost')) {
                $table->decimal('base_cost', 10, 2)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            // Restore old structure
            if (!Schema::hasColumn('shipping_methods', 'code')) {
                $table->string('code')->after('name');
                $table->string('icon')->nullable()->after('description');
                $table->decimal('cost', 10, 2)->nullable()->after('icon');
                $table->enum('cost_type', ['fixed', 'per_kg'])->after('cost');
                $table->integer('order')->default(0)->after('is_active');
            }
            
            if (Schema::hasColumn('shipping_methods', 'base_cost')) {
                $table->dropColumn('base_cost');
            }
        });
    }
};
