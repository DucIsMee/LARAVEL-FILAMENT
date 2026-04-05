<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * Tên bảng bắt đầu bằng MSSV
     */
    protected $table = '23810310110_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Tự động tạo slug từ name nếu slug chưa được set.
     * Được gọi từ CategoryResource khi lưu form.
     */
    public static function generateSlug(string $name): string
    {
        return Str::slug($name);
    }
}
