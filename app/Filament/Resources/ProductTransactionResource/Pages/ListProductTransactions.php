<?php

namespace App\Filament\Resources\ProductTransactionResource\Pages;

use App\Filament\Resources\ProductTransactionResource;
use App\Filament\Resources\ProductTransactionResource\Widgets\TransactionStats;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;


class ListProductTransactions extends ListRecords
{
    protected static string $resource = ProductTransactionResource::class;
    use ExposesTableToWidgets;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

             Action::make('prinf_pdf')
                ->label('Print Laporan')
                ->action(function($livewire){
                    $records = $livewire->getFilteredTableQuery()->get();
                    $filters = data_get($livewire, 'tableFilters',[]);
                    $fromDate = data_get($filters, 'created_at.from');
                    $untilDate = data_get($filters, 'created_at.until');

                    $pdf = Pdf::loadView('pdf.report',[
                        'records' => $records,
                        'fromDate' => $fromDate,
                        'untilDate' => $untilDate
                    ]);
                    return response()->streamDownload(fn()=> print($pdf->output()), 'report.pdf');
                })
        ];
    }
//     protected function getHeaderWidgets(): array
//     {
//     return [
//         TransactionStats::class,
//     ];
// }
}