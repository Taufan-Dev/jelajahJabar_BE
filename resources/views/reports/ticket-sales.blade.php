<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 3px double #00bc7d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #00bc7d;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 10px;
            color: #666;
            margin: 2px 0 0 0;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #00bc7d;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 6px 8px;
            border: 1px solid #00a36c;
        }
        .report-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .summary-box {
            border: 1px solid #00bc7d;
            background-color: #f0fdf4;
            padding: 10px 15px;
            width: 250px;
            float: right;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 10px;
            color: #166534;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #14532d;
        }
        .clearfix {
            clear: both;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 200px;
            float: right;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="header-title">Jelajah Jabar</h1>
                    <p class="header-subtitle">Sistem Pemesanan Tiket & Rekomendasi Wisata Jawa Barat</p>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    <div style="font-size: 12px; font-weight: bold; color: #555;">{{ $title }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%" class="bold">Dicetak Oleh:</td>
            <td width="35%">{{ $user->name }} ({{ strtoupper($user->role) }})</td>
            <td width="15%" class="bold">Tanggal Cetak:</td>
            <td width="35%">{{ now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
        <tr>
            <td class="bold">Cakupan Laporan:</td>
            <td>
                @if ($user->role === 'super_admin')
                    Seluruh Wilayah Jawa Barat
                @elseif ($user->role === 'admin_wilayah')
                    Kabupaten {{ $user->wilayah->nama_kabupaten ?? 'Wilayah Kerja' }}
                @else
                    Khusus Wisata Pengelola
                @endif
            </td>
            <td class="bold">Total Record:</td>
            <td>{{ $records->count() }} Data Transaksi</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Kode Tiket</th>
                <th width="18%">Pengunjung</th>
                <th width="20%">Wisata Tujuan</th>
                <th width="10%" class="text-center">Jml Tiket</th>
                <th width="12%" class="text-right">Harga Tiket</th>
                <th width="13%" class="text-right">Total Bayar</th>
                <th width="10%" class="text-center">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalTiket = 0;
                $totalPendapatan = 0;
            @endphp
            @forelse ($records as $index => $record)
                @php
                    $totalTiket += $record->jumlah_tiket;
                    if ($record->status_pembayaran === 'paid') {
                        $totalPendapatan += $record->total_harga;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="bold">{{ $record->kode_tiket }}</td>
                    <td>{{ $record->user->name ?? 'N/A' }}</td>
                    <td>{{ $record->wisata->nama_wisata ?? 'N/A' }}</td>
                    <td class="text-center">{{ $record->jumlah_tiket }}</td>
                    <td class="text-right">Rp {{ number_format($record->wisata->harga_tiket ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right bold">Rp {{ number_format($record->total_harga, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $record->status_pembayaran === 'paid' ? 'badge-success' : ($record->status_pembayaran === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $record->status_pembayaran }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data laporan yang sesuai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix"></div>

    <div class="summary-box">
        <div class="summary-title">Total Pendapatan Lunas</div>
        <div class="summary-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        <div style="font-size: 9px; margin-top: 5px; color: #4b5563;">
            Total Tiket Terjual: <strong>{{ $totalTiket }} Tiket</strong>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p style="font-size: 9px; color: #666; margin-top: -10px;">{{ strtoupper($user->role) }}</p>
            <div class="signature-line">{{ $user->name }}</div>
        </div>
    </div>

</body>
</html>
