<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('size_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('qty')->default(0);
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->string('sku')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
