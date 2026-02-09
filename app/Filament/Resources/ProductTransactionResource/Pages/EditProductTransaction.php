<?php

namespace App\Filament\Resources\ProductTransactionResource\Pages;

use App\Filament\Resources\ProductTransactionResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Produk;
use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;

class EditProductTransaction extends EditRecord
{
    protected static string $resource = ProductTransactionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        DB::beginTransaction();

        $oldRecord = $this->record;

        Produk::where('id', $oldRecord->produk_id)
            ->increment('stock', $oldRecord->quantity);

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

        $subTotal = $produk->price * $data['quantity'];
        $grandTotal = $subTotal;

        if (! empty($data['promo_code_id'])) {
            $promo = PromoCode::find($data['promo_code_id']);

            if (! $promo) {
                DB::rollBack();
                Notification::make()->title('Promo tidak ditemukan')->danger()->send();
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

    protected function afterSave(): void
    {
        Produk::where('id', $this->record->produk_id)
            ->decrement('stock', $this->record->quantity);

        DB::commit();
    }

     protected function getHeaderActions(): array
    {
        return [
        DeleteAction::make()
            ->before(function () {
                DB::beginTransaction();

                Produk::where('id', $this->record->produk_id)
                    ->increment('stock', $this->record->quantity);
            })
            ->after(function () {
                DB::commit();
            }),
        ];
    }
}
