<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>تم تقديم الطلب بنجاح</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .success-icon {
            color: #00BCD4;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #00BCD4;
            border-color: #00BCD4;
        }
        .btn-primary:hover {
            background-color: #00ACC1;
            border-color: #00ACC1;
        }
        .btn-outline-primary {
            color: #00BCD4;
            border-color: #00BCD4;
        }
        .btn-outline-primary:hover {
            background-color: #00BCD4;
            border-color: #00BCD4;
        }
        h2 {
            color: #00838F;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
        </div>
        <h2>تم تقديم طلب الرخصة بنجاح</h2>
        <p>شكراً لك! سيتم مراجعة طلبك من قبل الإدارة المختصة وسيتم التواصل معك قريباً</p>
        <div class="btn-group">
            <a href="https://baldy.masarfezzan.com/" class="btn btn-primary">العودة للرئيسية</a>
            <a href="{{ route('license-requests.create') }}" class="btn btn-outline-primary">تقديم طلب جديد</a>
        </div>
    </div>

    <script>
        // إعادة توجيه تلقائي بعد 5 ثواني
        setTimeout(function() {
            window.location.href = 'https://baldy.masarfezzan.com/';
        }, 5000);
    </script>
</body>
</html>
