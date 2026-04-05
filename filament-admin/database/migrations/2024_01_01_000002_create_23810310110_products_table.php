<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tên bảng bắt đầu bằng MSSV: 23810310110_products
     */
    public function up(): void
    {
        Schema::create('23810310110_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('23810310110_categories')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('price');           // VNĐ, không âm
            $table->unsignedInteger('stock_quantity');     // số nguyên không âm
            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'published', 'out_of_stock'])->default('draft');

            // ===== TRƯỜNG SÁNG TẠO: discount_percent =====
            // Lưu % giảm giá (0–100). Logic tính giá sau giảm giá được thực hiện
            // trong accessor $product->final_price
            $table->unsignedTinyInteger('discount_percent')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('23810310110_products');
    }
};
