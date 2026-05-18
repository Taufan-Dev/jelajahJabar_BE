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
        .rank-badge {
            display: inline-block;
            width: 18px;
            height: 18px;
            line-height: 18px;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        .rank-1 {
            background-color: #fef08a;
            color: #854d0e;
            border: 1px solid #eab308;
        }
        .rank-2 {
            background-color: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .rank-3 {
            background-color: #ffedd5;
            color: #9a3412;
            border: 1px solid #f97316;
        }
        .rank-other {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
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
            <td class="bold">Cakupan Wilayah:</td>
            <td>
                @if ($user->role === 'super_admin')
                    Seluruh Wilayah Jawa Barat
                @elseif ($user->role === 'admin_wilayah')
                    Kabupaten {{ $user->wilayah->nama_kabupaten ?? 'Wilayah Kerja' }}
                @else
                    Khusus Wisata Pengelola
                @endif
            </td>
            <td class="bold">Algoritma Urutan:</td>
            <td>Tiket Terjual Terbanyak & Rating Tertinggi</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th width="8%" class="text-center">Rank</th>
                <th width="22%">Nama Tempat Wisata</th>
                <th width="20%">Kabupaten/Kota</th>
                <th width="15%" class="text-center">Tiket Terjual</th>
                <th width="15%" class="text-center">Rating Rata-rata</th>
                <th width="20%" class="text-right">Omset Tiket</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $wisata)
                @php
                    $rank = $index + 1;
                    $class = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                @endphp
                <tr>
                    <td class="text-center">
                        <span class="rank-badge {{ $class }}">{{ $rank }}</span>
                    </td>
                    <td class="bold">{{ $wisata->nama_wisata }}</td>
                    <td>{{ $wisata->wilayah->nama_kabupaten ?? 'N/A' }}</td>
                    <td class="text-center bold">{{ $wisata->total_terjual ?? 0 }} Tiket</td>
                    <td class="text-center">
                        <span style="color: #eab308; font-size: 12px;">★</span> 
                        <strong>{{ number_format($wisata->rata_rating ?? 0, 1) }}</strong> / 5.0
                    </td>
                    <td class="text-right bold">
                        Rp {{ number_format(($wisata->total_terjual ?? 0) * $wisata->harga_tiket, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data wisata yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
