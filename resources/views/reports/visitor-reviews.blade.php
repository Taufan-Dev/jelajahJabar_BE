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
        .star-rating {
            color: #eab308;
            font-weight: bold;
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
            <td class="bold">Cakupan Ulasan:</td>
            <td>
                @if ($user->role === 'super_admin')
                    Seluruh Jawa Barat
                @elseif ($user->role === 'admin_wilayah')
                    Kabupaten {{ $user->wilayah->nama_kabupaten ?? 'Wilayah Kerja' }}
                @else
                    Khusus Tempat Wisata Pengelola
                @endif
            </td>
            <td class="bold">Total Ulasan:</td>
            <td>{{ $records->count() }} Feedback Pengunjung</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Nama Pengunjung</th>
                <th width="20%">Wisata yang Diulas</th>
                <th width="12%" class="text-center">Rating</th>
                <th width="38%">Isi Ulasan & Feedback</th>
                <th width="10%" class="text-center">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="bold">{{ $record->user->name ?? 'N/A' }}</td>
                    <td class="bold">{{ $record->wisata->nama_wisata ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="star-rating">★</span> {{ number_format($record->rating, 1) }} / 5.0
                    </td>
                    <td style="font-style: italic; color: #4b5563;">"{{ $record->ulasan ?? 'Tidak ada komentar tertulis.' }}"</td>
                    <td class="text-center">{{ $record->created_at->translatedFormat('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada ulasan pengunjung.</td>
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
