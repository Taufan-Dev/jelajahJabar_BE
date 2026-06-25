<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Tiket Jelajah Jabar</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .email-header {
            background: linear-gradient(135deg, #4F46E5 0%, #0EA5E9 100%);
            padding: 35px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
            color: #374151;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111827;
        }
        .intro-text {
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 25px;
        }
        .ticket-summary {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-row {
            margin-bottom: 12px;
            font-size: 14px;
            overflow: hidden;
        }
        .summary-row:last-child {
            margin-bottom: 0;
        }
        .summary-label {
            float: left;
            width: 40%;
            color: #6b7280;
            font-weight: 500;
        }
        .summary-value {
            float: left;
            width: 60%;
            color: #1f2937;
            font-weight: 700;
        }
        .highlight-value {
            color: #4F46E5;
        }
        .qr-info-box {
            text-align: center;
            border: 2px dashed #4F46E5;
            background-color: #f0f0ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .qr-info-title {
            font-size: 15px;
            font-weight: 700;
            color: #4F46E5;
            margin: 0 0 5px 0;
        }
        .qr-info-desc {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }
        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>{{ $isSimulasi ? 'PEMBAYARAN SIMULASI BERHASIL' : 'PEMBAYARAN BERHASIL' }}</h1>
                <p>E-Tiket Jelajah Jabar Anda Telah Terbit</p>
            </div>
            
            <div class="email-body">
                <div class="greeting">Halo, {{ $user->name }}!</div>
                <p class="intro-text">Terima kasih telah melakukan pemesanan tiket melalui Jelajah Jabar. Pembayaran Anda telah terkonfirmasi secara aman oleh sistem kami. Berikut adalah rincian tiket kunjungan Anda:</p>
                
                <div class="ticket-summary">
                    <div class="summary-title">Ringkasan E-Tiket</div>
                    
                    <div class="summary-row">
                        <div class="summary-label">Destinasi Wisata</div>
                        <div class="summary-value highlight-value">{{ $wisata->nama_wisata }}</div>
                    </div>
                    
                    <div class="summary-row">
                        <div class="summary-label">Kode E-Tiket</div>
                        <div class="summary-value" style="font-family: monospace; font-size: 15px; letter-spacing: 1px;">{{ $tiket->kode_tiket }}</div>
                    </div>
                    
                    <div class="summary-row">
                        <div class="summary-label">Tanggal Kunjungan</div>
                        <div class="summary-value">{{ $tiket->tanggal_kunjungan->translatedFormat('d F Y') }}</div>
                    </div>
                    
                    <div class="summary-row">
                        <div class="summary-label">Jumlah Tiket</div>
                        <div class="summary-value">{{ $tiket->jumlah_tiket }} Orang</div>
                    </div>
                    
                    <div class="summary-row">
                        <div class="summary-label">Total Pembayaran</div>
                        <div class="summary-value">Rp {{ number_format($tiket->total_harga, 0, ',', '.') }} (Lunas)</div>
                    </div>
                </div>
                
                <div class="qr-info-box">
                    <div class="qr-info-title">E-Tiket PDF Terlampir</div>
                    <p class="qr-info-desc">Kami melampirkan file PDF E-Tiket resmi Anda di email ini. Silakan unduh dan tunjukkan QR Code pada file PDF tersebut kepada petugas pintu masuk wisata.</p>
                </div>
            </div>
            
            <div class="email-footer">
                <p>Email ini dikirim secara otomatis oleh sistem Jelajah Jabar.</p>
                <p>&copy; {{ date('Y') }} Jelajah Jabar. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
