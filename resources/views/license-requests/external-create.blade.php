<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>طلب رخصة جديد</title>
    <script>
        // التأكد من أننا لسنا في إطار
        if (window.top !== window.self) {
            window.top.location.href = window.self.location.href;
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        .form-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px auto;
            max-width: 900px;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .progress {
            height: 8px;
            margin: 20px 0 40px;
            background-color: #e9ecef;
        }
        .progress-bar {
            background-color: #00BCD4; /* لون Cyan */
        }
        .btn-primary {
            background-color: #00BCD4; /* لون Cyan */
            border-color: #00BCD4;
        }
        .btn-primary:hover {
            background-color: #00ACC1; /* لون Cyan أغمق قليلاً */
            border-color: #00ACC1;
        }
        .btn-success {
            background-color: #00BCD4; /* لون Cyan */
            border-color: #00BCD4;
        }
        .btn-success:hover {
            background-color: #00ACC1; /* لون Cyan أغمق قليلاً */
            border-color: #00ACC1;
        }
        .btn-secondary {
            background-color: #B2EBF2; /* لون Cyan فاتح */
            border-color: #B2EBF2;
            color: #00838F;
        }
        .btn-secondary:hover {
            background-color: #80DEEA; /* لون Cyan فاتح أغمق قليلاً */
            border-color: #80DEEA;
            color: #006064;
        }
        .form-control:focus, .form-select:focus {
            border-color: #00BCD4;
            box-shadow: 0 0 0 0.25rem rgba(0, 188, 212, 0.25);
        }
        h4 {
            color: #00838F; /* لون Cyan غامق */
        }
        .text-danger {
            color: #00ACC1 !important; /* لون Cyan للنجمة */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">طلب رخصة جديد</h2>
            
            <div class="step-indicator">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 33%"></div>
                </div>
            </div>

            <form id="licenseRequestForm" action="{{ route('license-requests.store') }}" method="POST" target="_top">
                @csrf
                
                <!-- الخطوة 1: البيانات الشخصية -->
                <div class="step active" id="step1">
                    <h4 class="mb-4">البيانات الشخصية</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullName" class="form-label">الاسم الرباعي</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required maxlength="120">
                            <div class="invalid-feedback">يجب أن يحتوي الاسم على أحرف عربية فقط.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nationalID" class="form-label">الرقم الوطني</label>
                            <input type="text" class="form-control" id="nationalID" name="nationalID" required maxlength="20">
                            <div class="invalid-feedback">الرقم الوطني يجب أن يبدأ بـ 119 أو 120 أو 219 أو 220 ويتكون من 12 رقمًا.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passportOrID" class="form-label">رقم جواز السفر أو البطاقة</label>
                            <input type="text" class="form-control" id="passportOrID" name="passportOrID" maxlength="20">
                            <div class="invalid-feedback">رقم جواز السفر أو البطاقة يجب أن يتكون من 5 إلى 10 حروف أو أرقام إنجليزية فقط.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phoneNumber" class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required maxlength="15">
                            <div class="invalid-feedback">رقم الهاتف يجب أن يكون بصيغة ليبية (+218 أو 0 تليها 9 أرقام).</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="120"
                                   pattern="^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$">
                            <div class="invalid-feedback">البريد الإلكتروني يجب أن يكون صحيحًا.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-primary btn-next">التالي</button>
                    </div>
                </div>

                <!-- الخطوة 2: بيانات النشاط -->
                <div class="step" id="step2">
                    <h4 class="mb-4">بيانات النشاط</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="projectName" class="form-label">اسم النشاط</label>
                            <input type="text" class="form-control" id="projectName" name="projectName" required maxlength="120">
                            <div class="invalid-feedback">اسم النشاط يجب أن يحتوي على أحرف عربية فقط.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nearestLandmark" class="form-label">تخصص النشاط</label>
                            <input type="text" class="form-control" id="nearestLandmark" name="nearestLandmark" required maxlength="180">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="positionInProject" class="form-label">الصفة في النشاط</label>
                            <select class="form-select" id="positionInProject" name="positionInProject" required>
                                <option value="">اختر الصفة</option>
                                <option value="owner">صاحب النشاط</option>
                                <option value="general_manager">مدير عام</option>
                                <option value="chairman">رئيس مجلس الإدارة</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="projectAddress" class="form-label">عنوان النشاط</label>
                            <input type="text" class="form-control" id="projectAddress" name="projectAddress" required maxlength="160" placeholder="الشارع - رقم المبنى">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary btn-prev">السابق</button>
                        <button type="button" class="btn btn-primary btn-next">التالي</button>
                    </div>
                </div>

                <!-- الخطوة 3: تفاصيل الرخصة -->
                <div class="step" id="step3">
                    <h4 class="mb-4">تفاصيل الرخصة</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="municipality_id" class="form-label">البلدية</label>
                            <select class="form-select" id="municipality_id" name="municipality_id" required>
                                <option value="">اختر البلدية</option>
                                @foreach($municipalities as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region_id" class="form-label">المحلة</label>
                            <select class="form-select" id="region_id" name="region_id" required>
                                <option value="">اختر المحلة</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_type_id" class="form-label">نوع الترخيص</label>
                            <select class="form-select" id="license_type_id" name="license_type_id" required>
                                <option value="">اختر نوع الترخيص</option>
                                @foreach($licenseTypes as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary btn-prev">السابق</button>
                        <button type="submit" class="btn btn-success btn-submit">تقديم الطلب</button>
                    </div>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="https://baldy.masarfezzan.com/" target="_top" class="btn btn-secondary">العودة للرئيسية</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // تعريف دوال التحقق خارج نطاق jQuery ready
        function validateFullName(input) {
            const regex = /^[\u0621-\u064A\s]+$/;
            return validateInput(input, regex, "يجب أن يحتوي الاسم على أحرف عربية فقط.");
        }

        function validateNationalID(input) {
            const regex = /^(119|120|219|220)\d{9}$/;
            return validateInput(input, regex, "الرقم الوطني يجب أن يبدأ بـ 1 او 2 ويتكون من 12 رقمًا.");
        }

        function validatePassportOrID(input) {
            const regex = /^[A-Za-z0-9]{5,10}$/;
            return validateInput(input, regex, "رقم جواز السفر أو البطاقة يجب أن يتكون من 5 إلى 10 حروف أو أرقام إنجليزية فقط.");
        }

        function validatePhoneNumber(input) {
            const regex = /^(\+218|0)\d{9}$/;
            return validateInput(input, regex, "رقم الهاتف يجب أن يكون بصيغة ليبية (+218 أو 0 تليها 9 أرقام).");
        }

        function validateProjectName(input) {
            const regex = /^[\u0621-\u064A\s]+$/;
            return validateInput(input, regex, "اسم النشاط يجب أن يحتوي على أحرف عربية فقط.");
        }

        function validateInput(input, regex, errorMessage) {
            const isValid = regex.test(input.value);
            const $input = $(input);
            const $feedback = $input.siblings('.invalid-feedback');
            
            if (!isValid) {
                $input.addClass('is-invalid');
                $feedback.text(errorMessage);
            } else {
                $input.removeClass('is-invalid');
                $feedback.text('');
            }
            return isValid;
        }

        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 3;

            // إضافة مستمعي الأحداث للحقول
            $('#fullName').on('input', function() {
                validateFullName(this);
            });

            $('#nationalID').on('input', function() {
                validateNationalID(this);
            });

            $('#passportOrID').on('input', function() {
                validatePassportOrID(this);
            });

            $('#phoneNumber').on('input', function() {
                validatePhoneNumber(this);
            });

            $('#projectName').on('input', function() {
                validateProjectName(this);
            });

            // وظائف التنقل بين الخطوات
            function updateProgress() {
                const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                $('.progress-bar').css('width', progress + '%');
            }

            function showStep(step) {
                $('.step').removeClass('active');
                $('#step' + step).addClass('active');
                updateProgress();
            }

            // زر التالي
            $('.btn-next').click(function() {
                let isValid = true;
                const currentStepElement = $('#step' + currentStep);
                
                // التحقق من الحقول المطلوبة في الخطوة الحالية
                currentStepElement.find('input[required], select[required]').each(function() {
                    const $input = $(this);
                    if (!$input.val()) {
                        isValid = false;
                        $input.addClass('is-invalid');
                    }
                });

                // التحقق من صحة التنسيق في الخطوة الأولى
                if (currentStep === 1) {
                    if (!validateFullName($('#fullName')[0])) isValid = false;
                    if (!validateNationalID($('#nationalID')[0])) isValid = false;
                    if ($('#passportOrID').val() && !validatePassportOrID($('#passportOrID')[0])) isValid = false;
                    if (!validatePhoneNumber($('#phoneNumber')[0])) isValid = false;
                }

                // التحقق من صحة التنسيق في الخطوة الثانية
                if (currentStep === 2) {
                    if (!validateProjectName($('#projectName')[0])) isValid = false;
                }

                if (isValid && currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                } else if (!isValid) {
                    //ابقى في مكانك
                    
                }
            });

            // زر السابق
            $('.btn-prev').click(function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            // تحديث المحلات عند اختيار البلدية
            $('#municipality_id').change(function() {
                const municipalityId = $(this).val();
                const regionSelect = $('#region_id');
                
                if (municipalityId) {
                    regionSelect.prop('disabled', true);
                    regionSelect.empty().append('<option value="">جاري تحميل المحلات...</option>');
                    
                    $.ajax({
                        url: `/regions/${municipalityId}`,
                        method: 'GET',
                        success: function(response) {
                            console.log('Response from server:', response); // تسجيل الاستجابة
                            
                            regionSelect.empty().append('<option value="">اختر المحلة</option>');
                            
                            try {
                                if (typeof response === 'string') {
                                    response = JSON.parse(response);
                                }
                                
                                if (Array.isArray(response)) {
                                    response.forEach(function(region) {
                                        if (region && region.id && region.name) {
                                            regionSelect.append(`<option value="${region.id}">${region.name}</option>`);
                                        }
                                    });
                                } else if (typeof response === 'object') {
                                    for (const [id, name] of Object.entries(response)) {
                                        regionSelect.append(`<option value="${id}">${name}</option>`);
                                    }
                                }
                            } catch (error) {
                                console.error('Error parsing response:', error);
                                regionSelect.empty()
                                    .append('<option value="">خطأ في تنسيق البيانات</option>')
                                    .prop('disabled', true);
                            }
                            
                            regionSelect.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error details:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            regionSelect.empty()
                                .append('<option value="">حدث خطأ في تحميل المحلات</option>')
                                .prop('disabled', true);
                        }
                    });
                } else {
                    regionSelect.empty()
                        .append('<option value="">اختر البلدية أولاً</option>')
                        .prop('disabled', true);
                }
            });

            // التحقق من النموذج عند التقديم
            $('#licenseRequestForm').on('submit', function(e) {
                let isValid = true;
                
                // التحقق من جميع الحقول المطلوبة
                $(this).find('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    }
                });

                // التحقق من صحة التنسيق لكل حقل
                if (!validateFullName($('#fullName')[0])) isValid = false;
                if (!validateNationalID($('#nationalID')[0])) isValid = false;
                if ($('#passportOrID').val() && !validatePassportOrID($('#passportOrID')[0])) isValid = false;
                if (!validatePhoneNumber($('#phoneNumber')[0])) isValid = false;
                if (!validateProjectName($('#projectName')[0])) isValid = false;

                if (!isValid) {
                    e.preventDefault();
                    alert('يرجى تصحيح الأخطاء قبل إرسال النموذج');
                }
            });

            // معالجة تقديم النموذج
            $('#licenseRequestForm').on('submit', function(e) {
                e.preventDefault();
                
                // إزالة رسائل الخطأ السابقة
                $('.is-invalid').removeClass('is-invalid');
                
                // التحقق من جميع الحقول المطلوبة
                let isValid = true;
                $(this).find('input[required], select[required]').each(function() {
                    const $input = $(this);
                    if (!$input.val()) {
                        isValid = false;
                        $input.addClass('is-invalid');
                    }
                });

                if (!isValid) {
                    alert('الرجاء ملء جميع الحقول المطلوبة');
                    return false;
                }

                // تعطيل زر التقديم لمنع التقديم المتكرر
                const submitButton = $(this).find('button[type="submit"]');
                const originalText = submitButton.text();
                submitButton.prop('disabled', true).text('جاري الحفظ...');

                // إرسال النموذج
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        if (response.success) {
                            alert('تم تقديم الطلب بنجاح');
                            // التوجيه إلى صفحة النجاح أولاً
                            window.location.href = "{{ route('license-requests.success') }}";
                        } else {
                            window.location.href = "{{ route('license-requests.success') }}";
                        }
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false).text(originalText);
                        
                        console.log('Error response:', xhr.responseJSON);
                        
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMessage = 'يرجى تصحيح الأخطاء التالية:\n';
                            let errors = xhr.responseJSON.errors;
                            
                            Object.keys(errors).forEach(function(field) {
                                errorMessage += '- ' + errors[field][0] + '\n';
                                $(`[name="${field}"]`).addClass('is-invalid');
                            });
                            
                            alert(errorMessage);
                        } else {
                            alert('حدث خطأ أثناء تقديم الطلب. يرجى المحاولة مرة أخرى.');
                        }
                    }
                });
            });

            // إضافة مؤشرات بصرية للحقول المطلوبة
            $('input[required], select[required]').each(function() {
                const label = $('label[for="' + $(this).attr('id') + '"]');
                if (label.length) {
                    label.append(' <span class="text-danger">*</span>');
                }
            });

            // إزالة فئة is-invalid عند الكتابة في الحقل
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        });
        document.getElementById("licenseRequestForm").addEventListener("submit", function (e) {
    // إزالة الأخطاء السابقة
    const inputs = this.querySelectorAll("input, select");
    let isValid = true;

    inputs.forEach(function (input) {
        if (input.hasAttribute("required") && !input.value.trim()) {
            input.classList.add("is-invalid");
            isValid = false;
        } else {
            input.classList.remove("is-invalid");
        }
    });

    
});
    </script>
</body>
</html>
