# Filament Admin - Quản lý Danh mục & Sản phẩm
> **MSSV:** `23810310110`

---

## Cấu trúc file cần tạo/chỉnh sửa

```
app/
├── Filament/
│   └── Resources/
│       ├── CategoryResource.php
│       └── ProductResource.php
├── Models/
│   ├── Category.php
│   └── Product.php
├── Providers/
│   └── Filament/
│       └── AdminPanelProvider.php
database/
└── migrations/
    ├── xxxx_create_23810310110_categories_table.php
    └── xxxx_create_23810310110_products_table.php
```

---

## Bước cài đặt nhanh

```bash
# 1. Tạo project Laravel mới
composer create-project laravel/laravel filament-admin
cd filament-admin

# 2. Cấu hình .env
cp .env.example .env
# Sửa DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Cài Filament
composer require filament/filament:"^3.2" -W

# 4. Cài đặt Filament
php artisan filament:install --panels

# 5. Tạo user admin
php artisan make:filament-user

# 6. Chạy migration
php artisan migrate

# 7. Tạo storage link
php artisan storage:link

# 8. Chạy server
php artisan serve
```
