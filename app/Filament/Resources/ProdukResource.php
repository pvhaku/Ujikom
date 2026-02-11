<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Filament\Resources\ProdukResource\RelationManagers;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
         return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('products')
                    ->maxSize(2048)
                    ->required(),
                Forms\Components\Repeater::make('produkPhotos')
                    ->relationship('produkPhotos')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('products')
                            ->required(),
                    ])
                    ->columns(1),
                Forms\Components\Repeater::make('produkSizes')
                    ->relationship('produkSizes')
                    ->schema([
                        Forms\Components\TextInput::make('size')
                            ->required(),
                    ])
                    ->columns(1),

                    

            Section::make('Information Tambahan')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Textarea::make('about')
                                ->required(),
                            Forms\Components\Select::make('is_popular')
                                ->label('Is Popular')
                                ->required()
                                ->default(false)
                                ->options([
                                    '1' => 'Ganteng',
                                    '0' => 'Lumayan',
                                ]),
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required(),
                            Forms\Components\Select::make('brand_id')
                                ->relationship('brand', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('stock')
                                ->required()
                                ->numeric()
                                ->prefix('PCS'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->disk('public')
                    ->height(40)
                    ->width(40),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('produkSizes')
                    ->label('Size')
                    ->getStateUsing(fn($record) => $record->produkSizes->pluck('size')->join(', ')),
                Tables\Columns\TextColumn::make('produkPhotos')
                    ->label('Photos Count')
                    ->getStateUsing(fn($record) => $record->produkPhotos->count()),
                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_popular')
                    ->label('Popular'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),

        ];
    }
}