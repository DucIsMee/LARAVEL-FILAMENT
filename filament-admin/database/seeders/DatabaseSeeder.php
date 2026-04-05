<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo danh mục mẫu
        $categories = [
            ['name' => 'Điện thoại',    'description' => 'Điện thoại thông minh các loại'],
            ['name' => 'Laptop',         'description' => 'Máy tính xách tay'],
            ['name' => 'Phụ kiện',       'description' => 'Phụ kiện công nghệ'],
            ['name' => 'Đồng hồ thông minh', 'description' => 'Smartwatch các hãng'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['description'],
                'is_visible'  => true,
            ]);
        }

        // Tạo sản phẩm mẫu
        $products = [
            [
                'category_id'      => 1,
                'name'             => 'iPhone 15 Pro Max',
                'slug'             => 'iphone-15-pro-max',
                'description'      => '<p>Flagship mạnh nhất của Apple năm 2023.</p>',
                'price'            => 34990000,
                'stock_quantity'   => 50,
                'status'           => 'published',
                'discount_percent' => 10,   // giảm 10%
            ],
            [
                'category_id'      => 1,
                'name'             => 'Samsung Galaxy S24 Ultra',
                'slug'             => 'samsung-galaxy-s24-ultra',
                'description'      => '<p>Flagship Android với bút S-Pen.</p>',
                'price'            => 31990000,
                'stock_quantity'   => 30,
                'status'           => 'published',
                'discount_percent' => 0,
            ],
            [
                'category_id'      => 2,
                'name'             => 'MacBook Pro 14" M3',
                'slug'             => 'macbook-pro-14-m3',
                'description'      => '<p>Laptop chuyên nghiệp chip Apple M3.</p>',
                'price'            => 52990000,
                'stock_quantity'   => 15,
                'status'           => 'published',
                'discount_percent' => 5,    // giảm 5%
            ],
            [
                'category_id'      => 3,
                'name'             => 'AirPods Pro 2',
                'slug'             => 'airpods-pro-2',
                'description'      => '<p>Tai nghe không dây chống ồn chủ động.</p>',
                'price'            => 6990000,
                'stock_quantity'   => 0,
                'status'           => 'out_of_stock',
                'discount_percent' => 15,   // giảm 15%
            ],
            [
                'category_id'      => 4,
                'name'             => 'Apple Watch Series 9',
                'slug'             => 'apple-watch-series-9',
                'description'      => '<p>Đồng hồ thông minh cao cấp.</p>',
                'price'            => 12990000,
                'stock_quantity'   => 20,
                'status'           => 'draft',
                'discount_percent' => 0,
            ],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }

        $this->command->info('✅ Seeded ' . count($categories) . ' danh mục và ' . count($products) . ' sản phẩm.');
    }
}
