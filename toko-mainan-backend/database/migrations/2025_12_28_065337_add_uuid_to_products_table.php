<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });
        
        // Generate UUID for existing products
        DB::statement('UPDATE products SET uuid = UUID() WHERE uuid IS NULL');
        
        // Now make it unique and indexed
        Schema::table('products', function (Blueprint $table) {
            $table->unique('uuid');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
