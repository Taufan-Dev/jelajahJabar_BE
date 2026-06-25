<x-filament-panels::page>
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Validasi Tiket Pengunjung</h2>
        <p style="color: #6b7280; font-size: 0.875rem;">Arahkan QR Code ke kamera untuk melakukan validasi instan.</p>
    </div>
    
    <div class="scanner-wrapper">
        <div id="reader" class="scanner-video"></div>
        
        <div class="scanner-corners">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
        </div>

        <div id="idle-state" class="idle-state">
            <span class="idle-text">Kamera Mati</span>
        </div>

        <div id="scan-line" class="scan-line"></div>
    </div>
    
    <div class="btn-container">
        <button id="btn-start" type="button" class="custom-btn btn-start">Aktifkan Kamera</button>
        <button id="btn-stop" type="button" class="custom-btn btn-stop">Matikan Kamera</button>
    </div>

    <!-- Script HTML5-QRCode via CDN -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <style>
        .scanner-wrapper {
            position: relative;
            width: 280px;
            height: 280px;
            margin: 0 auto;
            background-color: #f3f4f6;
            border-radius: 1rem;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }
        .scanner-video {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0; left: 0;
            z-index: 10;
            display: none;
        }
        .scanner-video video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        .scanner-corners {
            position: absolute;
            top: 15px; bottom: 15px; left: 15px; right: 15px;
            z-index: 20;
            pointer-events: none;
        }
        .corner {
            position: absolute;
            width: 30px; height: 30px;
            border-color: #10b981; /* Filament primary green */
        }
        .corner-tl { top: 0; left: 0; border-top: 4px solid; border-left: 4px solid; border-top-left-radius: 8px; }
        .corner-tr { top: 0; right: 0; border-top: 4px solid; border-right: 4px solid; border-top-right-radius: 8px; }
        .corner-bl { bottom: 0; left: 0; border-bottom: 4px solid; border-left: 4px solid; border-bottom-left-radius: 8px; }
        .corner-br { bottom: 0; right: 0; border-bottom: 4px solid; border-right: 4px solid; border-bottom-right-radius: 8px; }
        
        .idle-state {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(249, 250, 251, 0.95);
            z-index: 30;
        }
        .idle-text {
            color: #6b7280;
            font-weight: 600;
            font-size: 16px;
        }

        .btn-container {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .custom-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: opacity 0.2s;
        }
        .custom-btn:hover { opacity: 0.8; }
        .btn-start { background-color: #10b981; }
        .btn-stop { background-color: #374151; display: none; }
        
        @keyframes scan {
            0% { top: 10%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 90%; opacity: 0; }
        }
        .scan-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: scan 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            z-index: 20;
            display: none;
        }
        
        /* html5-qrcode overrides to hide ugly default text/links */
        #reader img { display: none !important; }
        #reader span { display: none !important; }
        #reader a { display: none !important; }
        #reader__dashboard_section_csr { display: none !important; }
        #reader__dashboard_section_swaplink { display: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html5QrCode = new Html5Qrcode("reader");
            const btnStart = document.getElementById('btn-start');
            const btnStop = document.getElementById('btn-stop');
            const readerDiv = document.getElementById('reader');
            const idleState = document.getElementById('idle-state');
            const scanLine = document.getElementById('scan-line');
            
            let isScanningStatus = false;
            let isProcessing = false;

            function startScanner() {
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText, decodedResult) => {
                        if (isProcessing) return;
                        isProcessing = true;
                        
                        scanLine.style.display = 'none';
                        
                        @this.processScan(decodedText).then(() => {
                            setTimeout(() => {
                                isProcessing = false;
                                if(isScanningStatus) scanLine.style.display = 'block';
                            }, 2500);
                        });
                    },
                    (errorMessage) => { }
                ).then(() => {
                    isScanningStatus = true;
                    readerDiv.style.display = 'block';
                    idleState.style.display = 'none';
                    scanLine.style.display = 'block';
                    btnStart.style.display = 'none';
                    btnStop.style.display = 'block';
                }).catch((err) => {
                    console.log("Error starting camera", err);
                    alert("Kamera tidak dapat diakses.");
                });
            }

            function stopScanner() {
                if (isScanningStatus) {
                    html5QrCode.stop().then(() => {
                        isScanningStatus = false;
                        readerDiv.style.display = 'none';
                        idleState.style.display = 'flex';
                        scanLine.style.display = 'none';
                        btnStart.style.display = 'block';
                        btnStop.style.display = 'none';
                    }).catch(err => {
                        console.log("Error stopping camera", err);
                    });
                }
            }

            btnStart.addEventListener('click', startScanner);
            btnStop.addEventListener('click', stopScanner);
        });
    </script>
</x-filament-panels::page>
