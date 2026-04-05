<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    /**
     * Tên bảng bắt đầu bằng MSSV
     */
    protected $table = '23810310110_products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'status',
        'discount_percent',   // trường sáng tạo
    ];

    protected $casts = [
        'price'            => 'integer',
        'stock_quantity'   => 'integer',
        'discount_percent' => 'integer',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // -------------------------------------------------------
    // LOGIC TRƯỜNG SÁNG TẠO: discount_percent
    // -------------------------------------------------------

    /**
     * Tính giá sau khi áp dụng % giảm giá.
     * Ví dụ: price = 1.000.000, discount_percent = 20
     *        → final_price = 800.000
     *
     * @return int Giá cuối (VNĐ)
     */
    public function getFinalPriceAttribute(): int
    {
        if ($this->discount_percent <= 0) {
            return $this->price;
        }

        return (int) round($this->price * (1 - $this->discount_percent / 100));
    }

    /**
     * Kiểm tra sản phẩm có đang được giảm giá không.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_percent > 0;
    }

    /**
     * Số tiền được giảm (VNĐ).
     */
    public function getDiscountAmountAttribute(): int
    {
        return $this->price - $this->final_price;
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Định dạng price theo VNĐ.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return number_format($this->final_price, 0, ',', '.') . ' ₫';
    }
}
