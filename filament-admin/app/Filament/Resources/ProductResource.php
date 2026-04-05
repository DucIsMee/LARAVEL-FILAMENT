<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    // ===== SLUG của Resource bắt đầu bằng MSSV =====
    protected static ?string $slug = '23810310110-products';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Sản phẩm';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string $pluralModelLabel = 'Sản phẩm';

    protected static ?int $navigationSort = 2;

    // -------------------------------------------------------
    // FORM
    // -------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ===== CỘT TRÁI (2/3 chiều rộng) =====
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        // Rich Editor cho mô tả (yêu cầu bắt buộc)
                        Forms\Components\RichEditor::make('description')
                            ->label('Mô tả sản phẩm')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Giá & Tồn kho')
                    ->schema([
                        // Giá (VNĐ) — validation: không âm
                        Forms\Components\TextInput::make('price')
                            ->label('Giá (VNĐ)')
                            ->required()
                            ->numeric()
                            ->minValue(0)   // không được âm
                            ->step(1000)
                            ->prefix('₫')
                            ->helperText('Nhập giá gốc tính bằng đồng (VNĐ).'),

                        // Tồn kho — validation: số nguyên
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Số lượng tồn kho')
                            ->required()
                            ->numeric()
                            ->integer()     // phải là số nguyên
                            ->minValue(0)
                            ->suffix('sản phẩm'),

                        // ===== TRƯỜNG SÁNG TẠO: discount_percent =====
                        // Logic: giá hiển thị trên frontend = price * (1 - discount_percent/100)
                        Forms\Components\TextInput::make('discount_percent')
                            ->label('Giảm giá (%)')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->helperText('Nhập 0 nếu không giảm giá. Giá sau giảm sẽ được tính tự động.')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                // Preview giá sau giảm (lưu vào helper, không phải field DB)
                                // Logic thực tế nằm trong Model::getFinalPriceAttribute()
                            }),

                        // Preview giá sau giảm (chỉ đọc, computed)
                        Forms\Components\Placeholder::make('final_price_preview')
                            ->label('Giá sau giảm (preview)')
                            ->content(function (Forms\Get $get): string {
                                $price    = (int) ($get('price') ?? 0);
                                $discount = (int) ($get('discount_percent') ?? 0);

                                if ($price <= 0) {
                                    return '—';
                                }

                                $final = (int) round($price * (1 - $discount / 100));
                                return number_format($final, 0, ',', '.') . ' ₫'
                                    . ($discount > 0 ? " (tiết kiệm " . number_format($price - $final, 0, ',', '.') . " ₫)" : '');
                            }),
                    ])
                    ->columns(2),

            ])->columnSpan(2),

            // ===== CỘT PHẢI (1/3 chiều rộng) =====
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Ảnh đại diện')
                    ->schema([
                        // Upload 01 ảnh đại diện
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Ảnh sản phẩm')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(2048)   // 2MB
                            ->helperText('Tối đa 2MB. Định dạng: JPG, PNG, WebP.'),
                    ]),

                Forms\Components\Section::make('Phân loại')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set)
                                        => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft'        => 'Nháp',
                                'published'    => 'Đã đăng',
                                'out_of_stock' => 'Hết hàng',
                            ])
                            ->required()
                            ->default('draft'),
                    ]),

            ])->columnSpan(1),

        ])
        // Grid layout 3 cột (yêu cầu bắt buộc)
        ->columns(3);
    }

    // -------------------------------------------------------
    // TABLE
    // -------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Ảnh')
                    ->disk('public')
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()          // tìm kiếm theo tên (yêu cầu)
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                // Giá định dạng VNĐ (yêu cầu)
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá gốc')
                    ->formatStateUsing(fn (int $state): string =>
                        number_format($state, 0, ',', '.') . ' ₫'
                    )
                    ->sortable(),

                // Hiển thị discount_percent (trường sáng tạo)
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Giảm giá')
                    ->formatStateUsing(fn (int $state): string =>
                        $state > 0 ? "-{$state}%" : '—'
                    )
                    ->color(fn (int $state): string =>
                        $state > 0 ? 'danger' : 'gray'
                    )
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Tồn kho')
                    ->alignCenter()
                    ->sortable()
                    ->color(fn (int $state): string =>
                        $state === 0 ? 'danger' : ($state < 10 ? 'warning' : 'success')
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'gray'    => 'draft',
                        'success' => 'published',
                        'danger'  => 'out_of_stock',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'        => 'Nháp',
                        'published'    => 'Đã đăng',
                        'out_of_stock' => 'Hết hàng',
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Lọc theo danh mục (yêu cầu)
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                // Lọc theo trạng thái
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft'        => 'Nháp',
                        'published'    => 'Đã đăng',
                        'out_of_stock' => 'Hết hàng',
                    ]),

                // Lọc sản phẩm đang giảm giá (trường sáng tạo)
                Tables\Filters\Filter::make('on_sale')
                    ->label('Đang giảm giá')
                    ->query(fn ($query) => $query->where('discount_percent', '>', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // -------------------------------------------------------
    // PAGES
    // -------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
