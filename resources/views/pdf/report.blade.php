<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <tittle>Laporan Transaksi</tittle>
        <style>
            body{
                font-family:sans-serif;
            }
            table{
                border-collapse:collapse;
                margin:auto
            }
            .font-kecil{
                font-size:14px;
            }
            th, td{
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }
            th{
                background: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <h1>Laporan Transaksi Produk</h1>
        <div class="date-range">
            @if($fromDate && $untilDate)
            Dari Tanggal: {{ \Carbon\Carbon::parse($fromDate)-> format('d M Y') }}
            <br/>
            Sampai Tanggal: {{ \Carbon\Carbon::parse($untilDate)-> format('d M Y') }}
            @elseif($fromDate)
            Dari Tanggal: {{ \Carbon\Carbon::parse($fromDate)-> format('d M Y') }}
            @elseif($untilDate)
            Sampai Tanggal: {{ \Carbon\Carbon::parse($untilDate)-> format('d M Y') }}
            @else
            Tanggal Laporan: {{ now()->format('d M Y') }}\
            @endif
        </div>
        <table style="width: 85%; margin: 16px auto; border-collapse:collapse #ddd;">
            <tr>
                <td style="width: 33%; text-align:center; padding:10px; border:none;">
                    <div style="font-size:12px; color:#666;">Total Transaksi</div>
                    <strong>{{$records->count()}}</strong>
                </td>
                <td style="width: 33%; text-align:center; padding:10px; border:none;">
                    <div style="font-size:12px; color:#666;">Total Produk Terjual</div>
                    <strong>{{$records->sum('quantity')}}</strong>
                </td>
                <td style="width: 33%; text-align:center; padding:10px; border:none;">
                    <div style="font-size:12px; color:#666;">Total Pendapatan</div>
                    <strong>RP {{number_format($records->sum('grand_total_amount'),0,',','.')}}</strong>
                </td>
            </tr>
        </table>
        <br/>
        <table class="font-kecil">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Sub Total</th> 
                    <th>Diskon</th> 
                    <th>Total</th>
                    <th>Status Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{$record->booking_trx_id}}</td>
                        <td>{{$record->name}}</td>
                        <td>{{$record->produk->name}}</td>
                        <td>{{$record->quantity}}</td>
                        <td>RP {{number_format($record->sub_total_amount,0,',','.')}}</td>
                        <td>RP {{ number_format((int) ($record->promoCode?->discount_amount ?? 0), 0, ',', '.') }}</td>
                        <td>RP {{number_format($record->grand_total_amount,0,',','.')}}</td>
                        <td>{{$record->is_paid? 'Selesai' : 'Belum'}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body> 
</html>