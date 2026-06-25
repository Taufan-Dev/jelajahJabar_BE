<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Tiket Jelajah Jabar - {{ $tiket->kode_tiket }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 30px;
            background-color: #f3f4f6;
        }
        .ticket-card {
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px dashed #4F46E5;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            max-width: 500px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        .ticket-header {
            background-color: #4F46E5;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 10px;
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .ticket-body {
            padding: 25px;
        }
        .wisata-title {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label {
            color: #6b7280;
            font-weight: 500;
            width: 35%;
        }
        .value {
            color: #1f2937;
            font-weight: bold;
        }
        .qr-section {
            text-align: center;
            border-top: 1px dashed #ddd;
            padding-top: 20px;
            margin-top: 15px;
        }
        .qr-code {
            display: inline-block;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .ticket-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
            margin-top: 10px;
            letter-spacing: 2px;
        }
        .ticket-footer {
            background-color: #f9fafb;
            padding: 15px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }
        .circle-left, .circle-right {
            position: absolute;
            top: 200px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #f3f4f6;
            z-index: 10;
        }
        .circle-left {
            left: -10px;
        }
        .circle-right {
            right: -10px;
        }
    </style>
</head>
<body>

    <div class="ticket-card">
        <div class="circle-left"></div>
        <div class="circle-right"></div>
        
        <div class="ticket-header">
            <h1 class="header-title">E-Tiket Masuk</h1>
            <p class="header-subtitle">Jelajah Jabar - Sistem Tiket Resmi Digital</p>
        </div>

        <div class="ticket-body">
            <h2 class="wisata-title">{{ $wisata->nama_wisata }}</h2>
            
            <table class="info-table">
                <tr>
                    <td class="label">Nama Pengunjung</td>
                    <td class="value">: {{ $user->name }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Kunjungan</td>
                    <td class="value">: {{ $tiket->tanggal_kunjungan->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Tiket</td>
                    <td class="value">: {{ $tiket->jumlah_tiket }} Orang</td>
                </tr>
                <tr>
                    <td class="label">Total Pembayaran</td>
                    <td class="value">: Rp {{ number_format($tiket->total_harga, 0, ',', '.') }} (LUNAS)</td>
                </tr>
                <tr>
                    <td class="label">Metode Bayar</td>
                    <td class="value">: Midtrans Online Payment</td>
                </tr>
            </table>

            <div class="qr-section">
                <div class="qr-code">
                    {!! QrCode::size(140)->margin(0)->generate($tiket->kode_tiket) !!}
                </div>
                <div class="ticket-code">{{ $tiket->kode_tiket }}</div>
                <p style="font-size: 9px; color: #9ca3af; margin: 5px 0 0 0;">Tunjukkan QR Code ini pada petugas di pintu masuk wisata untuk di-scan.</p>
            </div>
        </div>

        <div class="ticket-footer">
            <p>E-Tiket ini diterbitkan secara resmi oleh Jelajah Jabar. Pintu masuk berhak menolak tiket ilegal.</p>
            <p>&copy; {{ date('Y') }} Jelajah Jabar. All Rights Reserved.</p>
        </div>
    </div>

</body>
</html>
