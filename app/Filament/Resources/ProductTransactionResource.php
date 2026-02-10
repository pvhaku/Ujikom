<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Models\ProductTransaction;
use Filament\Tables\Filters\Filter;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;



use App\Filament\Resources\ProductTransactionResource\Pages;
use App\Filament\Resources\ProductTransactionResource\RelationManagers;

class ProductTransactionResource extends Resource
{
    protected static ?string $model = ProductTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->numeric(),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email('email')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('post_code')
                    ->required(),
                FileUpload::make('proof')
                    ->required(),
                Select::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('produk_size', null)),
                Select::make('produk_size')
                    ->label('Size')
                    ->required()
                    ->options(function (callable $get) {
                        $produkId = $get('produk_id');
                        if (! $produkId) {
                                return [];
                            }
                        return \App\Models\ProdukSize::where('produk_id', $produkId)
                    ->pluck('size', 'id');
                    })
                    ->disabled(fn (callable $get) => ! $get('produk_id'))
                    ->reactive(),
                TextInput::make('address')
                    ->required()
                    ->label('Alamat'),
                TextInput::make('quantity')
                    ->numeric()
                    ->label('jumlah')
                    ->required(),
                Toggle::make('is_paid')
                    ->label('Status bayar')
                    ->required(),
                Select::make('promo_code_id')
                    ->relationship('promoCode','code'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label("No. Telp")
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable(),
                TextColumn::make('booking_trx_id')
                    ->label("ID Transaksi")
                    ->searchable(),
                TextColumn::make('promoCode.discount_amount')
                    ->label('Diskon')
                    ->getStateUsing(fn ($record) => $record->promoCode?->discount_amount ?? 'No Code'),
                TextColumn::make("grand_total_amount")
                    ->label("Total")
                    ->money('IDR'),
                TextColumn::make('is_paid')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => $state ? 'Selesai' : 'Belum' )
            ])
            // ->filters([
            //     TernaryFilter::make('is_paid')
            //         ->label('Status Pembayaran')
            //         ->trueLabel('Selesai')
            //         ->falseLabel('Belum Dibayar'),

            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until')
                    ])
                    ->query(function(Builder $query, array $data):Builder{
                        return $query
                            ->when($data['from'],fn($q,$date)=>$q->whereDate('created_at','>=',$date))
                            ->when($data['until'],fn($q,$date)=>$q->whereDate('created_at','<=',$date));
                    })
                    ->indicateUsing(function(array $data) : array{
                        $indicators= [];
                        if ($data['from'] ?? null){
                            $indicators['from']= "transaction from" . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null){
                            $indicators['until']= "transaction until" . Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('proof')
                    ->label('Download Proof')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ProductTransaction $record) => $record->proof ? asset('storage/' . $record->proof) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (ProductTransaction $record) => !empty($record->proof)),
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
            'index' => Pages\ListProductTransactions::route('/'),
            'create' => Pages\CreateProductTransaction::route('/create'),
            'edit' => Pages\EditProductTransaction::route('/{record}/edit'),
        ];
    }
}
