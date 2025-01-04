<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ماسح رمز QR</title>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            font-family: 'Cairo', sans-serif;
        }

        .scanner-container {
            width: 100%;
            max-width: 600px;
            padding: 1rem;
            margin: 0 auto;
            text-align: center;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto 1rem auto;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        #reader video {
            width: 100% !important;
            border-radius: 1rem;
        }

        .status-expired {
            background-color: #fee2e2 !important;
            transition: background-color 0.5s ease;
        }

        .status-active {
            background-color: #dcfce7 !important;
            transition: background-color 0.5s ease;
        }

        #error {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #fee2e2;
            color: #991b1b;
            display: none;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .logout-button {
            position: fixed;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #dc2626;
        }

        .scan-status {
            margin-top: 1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-button">تسجيل الخروج</button>
    </form>

    <div x-data="qrScanner" class="scanner-container">
        <h1 style="margin-bottom: 2rem; color: #374151; font-size: 1.5rem;">ماسح رمز QR</h1>
        <div id="reader"></div>
        <div id="error"></div>
        <div class="scan-status" x-text="scanStatus"></div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('qrScanner', () => ({
                html5QrCode: null,
                isScanning: false,
                scanStatus: 'جاري تهيئة الكاميرا...',

                init() {
                    this.html5QrCode = new Html5Qrcode("reader");
                    this.startScanning();
                },

                showError(message) {
                    const errorDiv = document.getElementById('error');
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                    setTimeout(() => {
                        errorDiv.style.display = 'none';
                    }, 3000);
                },

                verifyLicense(code) {
                    this.scanStatus = 'جاري التحقق من الترخيص...';
                    fetch('/verify-license', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ qr_code: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.body.className = data.is_expired ? 'status-expired' : 'status-active';
                            this.scanStatus = data.is_expired ? 'الترخيص منتهي' : 'الترخيص ساري المفعول';
                            
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 2000);
                        } else {
                            this.showError(data.message || 'حدث خطأ أثناء التحقق من الترخيص');
                            this.scanStatus = 'جاري المسح...';
                            this.startScanning();
                        }
                    })
                    .catch(error => {
                        this.showError('حدث خطأ أثناء الاتصال بالخادم');
                        this.scanStatus = 'جاري المسح...';
                        this.startScanning();
                    });
                },

                startScanning() {
                    if (!this.isScanning) {
                        this.isScanning = true;
                        this.scanStatus = 'جاري المسح...';
                        const config = {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        };

                        this.html5QrCode.start(
                            { facingMode: "environment" },
                            config,
                            (decodedText) => {
                                this.isScanning = false;
                                this.html5QrCode.stop();
                                this.verifyLicense(decodedText);
                            },
                            (errorMessage) => {
                                console.log(errorMessage);
                            }
                        )
                        .catch(err => {
                            this.showError('لا يمكن الوصول إلى الكاميرا. يرجى التحقق من الإذن.');
                            this.scanStatus = 'خطأ في الوصول للكاميرا';
                        });
                    }
                },

                stopScanning() {
                    if (this.isScanning) {
                        this.html5QrCode.stop()
                        .then(() => {
                            this.isScanning = false;
                            this.scanStatus = 'تم إيقاف المسح';
                        })
                        .catch(err => {
                            this.showError('حدث خطأ أثناء إيقاف المسح');
                        });
                    }
                }
            }));
        });
    </script>
</body>
</html>
