<?php

namespace App\Filament\Resources\ProductTransactionResource\Widgets;

use App\Filament\Resources\ProductTransactionResource\Pages\ListProductTransactions;
use App\Models\ProductTransaction;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget;

class TransactionStats extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = null;
    use InteractsWithPageTable;
    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();
        return[
            Stat::make('Total Transaksi',  $query->count()),
            Stat::make('Pendapatan','Rp ' . number_format($query->sum('grand_total_amount'), 0, ',', '.')),
            Stat::make('Produk Terjual',  $query->sum('quantity')),
        ];
    }
    protected function getTablePage(): string
    {
    return ListProductTransactions::class;
    }

}