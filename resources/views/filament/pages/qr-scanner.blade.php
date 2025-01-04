<x-filament-panels::page>
    <div class="space-y-6">
        <div class="filament-main-content">
            <div class="mx-auto p-2 max-w-7xl">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold tracking-tight text-gray-950 text-center mb-6">
                        ماسح رمز QR
                    </h2>

                    <!-- حاوية الكاميرا -->
                    <div id="reader" class="overflow-hidden rounded-lg mb-4"></div>
                    
                    <!-- نتيجة التحقق -->
                    <div id="result" class="mt-4 p-4 rounded-lg text-center font-medium hidden">
                        <div class="flex flex-col items-center space-y-2">
                            <div id="resultMessage" class="text-lg font-bold"></div>
                            <div id="licenseNumber" class="text-sm"></div>
                            <div id="expiryDate" class="text-sm"></div>
                            <a id="detailsButton" href="#" class="hidden filament-button inline-flex items-center justify-center py-2 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-gray-800 shadow focus:ring-white border-gray-300 bg-white hover:bg-gray-50">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>

                    <!-- الإدخال اليدوي -->
                    <div class="mt-6">
                        <form id="verifyForm" class="space-y-4">
                            @csrf
                            <div class="relative">
                                <input type="text" 
                                       id="licenseInput"
                                       name="qr_code"
                                       placeholder="أدخل رقم الرخصة يدوياً"
                                       class="block w-full rounded-lg border-gray-300 text-gray-950 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500"
                                       required
                                >
                            </div>
                            
                            <button type="submit"
                                    class="w-full filament-button inline-flex items-center justify-center py-2 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                                تحقق من الرخصة
                            </button>
                        </form>
                    </div>

                    <!-- رسائل الخطأ -->
                    <div id="error" class="fixed top-4 inset-x-4 p-4 rounded-lg bg-danger-50 text-danger-700 text-center shadow-lg hidden"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // بدء المسح
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    html5QrCode.stop();
                    verifyLicense(decodedText);
                },
                (error) => {
                    console.log(error);
                }
            ).catch((err) => {
                showError('لا يمكن الوصول إلى الكاميرا. يرجى التحقق من الإذن.');
            });

            // معالجة النموذج
            document.getElementById('verifyForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const licenseNumber = document.getElementById('licenseInput').value.trim();
                if (licenseNumber) {
                    verifyLicense(licenseNumber);
                } else {
                    showError('الرجاء إدخال رقم الرخصة');
                }
            });

            // التحقق من الرخصة
            function verifyLicense(code) {
                const resultDiv = document.getElementById('result');
                resultDiv.classList.add('hidden');

                const formData = new FormData();
                formData.append('qr_code', code);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("verify.license") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response:', data); // للتأكد من البيانات المستلمة
                    if (data.success) {
                        showResult(data);
                    } else {
                        showError(data.message || 'حدث خطأ أثناء التحقق من الرخصة');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('حدث خطأ أثناء الاتصال بالخادم');
                });
            }

            // عرض النتيجة
            function showResult(data) {
                const resultDiv = document.getElementById('result');
                const messageDiv = document.getElementById('resultMessage');
                const licenseDiv = document.getElementById('licenseNumber');
                const expiryDiv = document.getElementById('expiryDate');
                const detailsButton = document.getElementById('detailsButton');

                // تحديث المحتوى
                messageDiv.textContent = data.message;
                licenseDiv.textContent = 'رقم الرخصة: ' + data.license_number;
                expiryDiv.textContent = 'تاريخ الانتهاء: ' + data.expiry_date;

                // تحديث الألوان
                resultDiv.className = 'mt-4 p-4 rounded-lg text-center font-medium ' + 
                    (data.is_expired ? 'bg-danger-100 text-danger-700 border border-danger-300' : 
                                     'bg-success-100 text-success-700 border border-success-300');

                // تحديث زر التفاصيل
                if (data.redirect_url) {
                    detailsButton.href = data.redirect_url;
                    detailsButton.classList.remove('hidden');
                } else {
                    detailsButton.classList.add('hidden');
                }

                // إظهار النتيجة
                resultDiv.classList.remove('hidden');
            }

            // عرض الخطأ
            function showError(message) {
                const errorDiv = document.getElementById('error');
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                setTimeout(() => {
                    errorDiv.classList.add('hidden');
                }, 3000);
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
