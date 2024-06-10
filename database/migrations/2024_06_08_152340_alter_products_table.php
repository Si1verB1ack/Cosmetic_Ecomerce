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
        Schema::table('products',function(Blueprint $blueprint){
            $blueprint->text('short_description')->nullable()->after('description');
            $blueprint->text('shipping_returns')->nullable()->after('short_description');
            $blueprint->text('related_products')->nullable()->after('shipping_returns');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products',function(Blueprint $blueprint){
            $blueprint->dropColumn('short_description');
            $blueprint->dropColumn('shipping_returns');
            $blueprint->dropColumn('related_products');
        });
    }
};
