<?php

namespace App\Filament\Resources\ProductTransactionResource\Pages;

use App\Filament\Resources\ProductTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\PromoCode;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreateProductTransaction extends CreateRecord
{
    protected static string $resource = ProductTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        DB::beginTransaction();
        $produk = Produk::lockForUpdate()->find($data['produk_id']);
        if (! $produk) {
            DB::rollBack();
            Notification::make()->title('Produk tidak ditemukan')->danger()->send();
            $this->halt();
        }
        
        if ($produk->stock < $data['quantity']) {
            DB::rollBack();
            Notification::make()
                ->title('Stok tidak mencukupi')
                ->body('Stok tersedia: ' . $produk->stock)
                ->danger()
                ->send();
            $this->halt();
        }

        $data['booking_trx_id'] = (new \App\Models\ProductTransaction)
            ->generateUniqueTrxId();
        $subTotal = $produk->price * $data['quantity'];
        $grandTotal = $subTotal;

        if (! empty($data['promo_code_id'])) {
            $promo = \App\Models\PromoCode::find($data['promo_code_id']);
            if (! $promo) {
                DB::rollBack();
                Notification::make()
                    ->title('Promo tidak ditemukan')
                    ->danger()
                    ->send();
                $this->halt();
            }

            $grandTotal -= (int) $promo->discount_amount;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }

        }
        $data['sub_total_amount'] = $subTotal;
        $data['grand_total_amount'] = $grandTotal;

        return $data;
    }

    protected function afterCreate(): void
    {
        Produk::where('id', $this->record->produk_id)
            ->decrement('stock', $this->record->quantity);

        DB::commit();
    }

}
