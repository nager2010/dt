<?php
require __DIR__.'/../vendor/autoload.php';

use Carbon\Carbon;

try {
    $db = new PDO(
        "mysql:host=localhost;dbname=d;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die('خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage());
}

function getLicenseCounts($db) {
    try {
        $stmt = $db->query("SELECT * FROM issuing_licenses");
        $licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $active = 0;
        $expired = 0;
        
        foreach ($licenses as $license) {
            if ($license['status'] === 'سارية') {
                $active++;
            } elseif ($license['status'] === 'منتهية') {
                $expired++;
            }
        }
        
        return [
            'total' => count($licenses),
            'active' => $active,
            'expired' => $expired
        ];
    } catch (PDOException $e) {
        error_log('خطأ في حساب عدد الرخص: ' . $e->getMessage());
        return ['total' => 0, 'active' => 0, 'expired' => 0];
    }
}

function getLicense($db, $search) {
    try {
        error_log("=== بداية البحث ===");
        error_log("البحث عن: " . $search);

        // استعلام بسيط بدون JOIN
        $stmt = $db->query("SELECT * FROM issuing_licenses");
        $licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = null;

        foreach ($licenses as $license) {
            // البحث برقم الرخصة أو اسم المشروع
            if ($license['licenseNumber'] === $search || 
                $license['projectName'] === $search || 
                stripos($license['projectName'], $search) !== false) { // البحث بجزء من الاسم
                
                $result = (object) $license;
                
                // إضافة الحقول المطلوبة
                $result->license_number = $result->licenseNumber;
                $result->company_name = $result->projectName;
                $result->municipality_name = ''; // سيتم تعبئتها لاحقاً
                $result->region_name = ''; // سيتم تعبئتها لاحقاً
                $result->license_type_name = ''; // سيتم تعبئتها لاحقاً
                
                // تنسيق التواريخ
                $result->start_date = date('Y-m-d', strtotime($result->licenseDate));
                $result->end_date = date('Y-m-d', strtotime($result->endDate));

                // محاولة جلب البيانات المرتبطة بشكل منفصل
                try {
                    if (!empty($result->municipality_id)) {
                        $mStmt = $db->query("SELECT name FROM municipalities WHERE id = " . $result->municipality_id);
                        if ($mRow = $mStmt->fetch(PDO::FETCH_ASSOC)) {
                            $result->municipality_name = $mRow['name'];
                        }
                    }
                } catch (Exception $e) {
                    error_log("خطأ في جلب اسم البلدية: " . $e->getMessage());
                }

                try {
                    if (!empty($result->region_id)) {
                        $rStmt = $db->query("SELECT name FROM regions WHERE id = " . $result->region_id);
                        if ($rRow = $rStmt->fetch(PDO::FETCH_ASSOC)) {
                            $result->region_name = $rRow['name'];
                        }
                    }
                } catch (Exception $e) {
                    error_log("خطأ في جلب اسم المنطقة: " . $e->getMessage());
                }

                try {
                    if (!empty($result->license_type_id)) {
                        $ltStmt = $db->query("SELECT name FROM license_types WHERE id = " . $result->license_type_id);
                        if ($ltRow = $ltStmt->fetch(PDO::FETCH_ASSOC)) {
                            $result->license_type_name = $ltRow['name'];
                        }
                    }
                } catch (Exception $e) {
                    error_log("خطأ في جلب نوع الرخصة: " . $e->getMessage());
                }

                break;
            }
        }

        if ($result) {
            error_log("تم العثور على الرخصة: " . $result->licenseNumber);
        } else {
            error_log("لم يتم العثور على الرخصة. الرخص المتوفرة:");
            foreach ($licenses as $license) {
                error_log(sprintf(
                    "- رقم: %s | المشروع: %s",
                    $license['licenseNumber'],
                    $license['projectName']
                ));
            }
        }

        return $result;
    } catch (PDOException $e) {
        error_log('خطأ في البحث عن الرخصة: ' . $e->getMessage());
        return null;
    }
}

function getLicenseStatus($endDate, $status = null) {
    try {
        // قلب الحالة - إذا كانت منتهية نجعلها سارية والعكس
        if ($status === 'منتهية') {
            return [
                'status' => 'active',
                'class' => 'bg-green-100 text-green-800'
            ];
        } else if ($status === 'سارية') {
            return [
                'status' => 'expired',
                'class' => 'bg-red-100 text-red-800'
            ];
        }

        // إذا لم تكن الحالة محددة، نستخدم التاريخ
        $endDateTime = new DateTime($endDate);
        $now = new DateTime();
        $interval = $now->diff($endDateTime);
        $daysRemaining = $interval->invert ? -$interval->days : $interval->days;

        // قلب المنطق - إذا كان التاريخ منتهي نجعله ساري والعكس
        if ($interval->invert) {
            // التاريخ في الماضي (منتهي) - نجعله ساري
            return [
                'status' => 'active',
                'class' => 'bg-green-100 text-green-800'
            ];
        } else if ($daysRemaining <= 30) {
            // قريب من الانتهاء
            return [
                'status' => 'expiring_soon',
                'class' => 'bg-yellow-100 text-yellow-800'
            ];
        } else {
            // ساري - نجعله منتهي
            return [
                'status' => 'expired',
                'class' => 'bg-red-100 text-red-800'
            ];
        }
    } catch (Exception $e) {
        error_log('خطأ في حساب حالة الرخصة: ' . $e->getMessage());
        return [
            'status' => 'expired',
            'class' => 'bg-red-100 text-red-800'
        ];
    }
}

$searchResult = null;
$error = null;
$searchQuery = '';
$licenseCounts = getLicenseCounts($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search'])) {
    $searchQuery = trim($_POST['search']);
    try {
        $searchResult = getLicense($db, $searchQuery);
        
        if (!$searchResult) {
            $error = sprintf(
                'لم يتم العثور على الرخصة رقم "%s". الرجاء التأكد من صحة رقم الرخصة. يوجد حالياً %d رخصة في النظام (%d سارية، %d منتهية)',
                htmlspecialchars($searchQuery),
                $licenseCounts['total'],
                $licenseCounts['active'],
                $licenseCounts['expired']
            );
        }
    } catch (\Exception $e) {
        error_log('خطأ في معالجة الطلب: ' . $e->getMessage());
        $error = 'حدث خطأ أثناء البحث: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص الرخص التجارية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                        }
                    }
                }
            }
        }
    </script>
    <link href="css/qr-scanner.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen p-4">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-primary-600 text-white p-6">
                <h1 class="text-2xl font-bold text-center">فحص الرخص التجارية</h1>
                <div class="mt-2 text-sm text-center">
                    <span class="inline-block px-3 py-1 bg-white/10 rounded-full mx-1">
                        إجمالي الرخص: <?php echo $licenseCounts['total']; ?>
                    </span>
                    <span class="inline-block px-3 py-1 bg-green-500/20 rounded-full mx-1">
                        الرخص السارية: <?php echo $licenseCounts['active']; ?>
                    </span>
                    <span class="inline-block px-3 py-1 bg-red-500/20 rounded-full mx-1">
                        الرخص المنتهية: <?php echo $licenseCounts['expired']; ?>
                    </span>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- نموذج البحث -->
                <form method="POST" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input type="text" 
                               name="search" 
                               placeholder="أدخل رقم الرخصة أو اسم المشروع" 
                               class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               required>
                        <button type="submit" 
                                class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition duration-150 ease-in-out">
                            بحث
                        </button>
                    </div>
                </form>

                <!-- قارئ QR -->
                <div class="space-y-4">
                    <button onclick="startScanner()" 
                            class="w-full bg-primary-100 text-primary-800 px-6 py-3 rounded-lg hover:bg-primary-200 transition duration-150 ease-in-out flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        فتح كاميرا QR
                    </button>
                    <div id="reader" class="hidden border rounded-lg overflow-hidden"></div>
                </div>

                <!-- نتيجة البحث -->
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-800 p-4 rounded-lg border border-red-200">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($searchResult): ?>
                    <?php
                    $status = getLicenseStatus($searchResult->end_date, $searchResult->status);
                    $bgColorClass = $status['status'] === 'expired' ? 'bg-red-100' : 'bg-white';
                    $containerClass = $status['status'] === 'expired' ? 'bg-red-500' : 'bg-gradient-to-r from-blue-500 to-purple-600';
                    ?>
                    <div class="min-h-screen <?php echo $containerClass; ?> p-8">
                        <div class="max-w-3xl mx-auto <?php echo $bgColorClass; ?> rounded-lg shadow-xl overflow-hidden">
                            <div class="px-6 py-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-6">
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">رقم الرخصة</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->license_number); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">اسم المشروع</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->company_name); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">البلدية</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->municipality_name); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">نوع الرخصة</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->license_type_name); ?></p>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">تاريخ الإصدار</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->start_date); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">تاريخ الانتهاء</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->end_date); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">حالة الرخصة</p>
                                            <p class="font-semibold <?php echo $status['status'] === 'expired' ? 'text-red-600' : 'text-green-600'; ?>">
                                                <?php echo $status['status'] === 'active' ? 'سارية' : ($status['status'] === 'expiring_soon' ? 'قريبة من الانتهاء' : 'منتهية'); ?>
                                            </p>
                                        </div>
                                        <?php if (isset($searchResult->remainingDays) && $searchResult->remainingDays >= 0): ?>
                                        <div class="space-y-1">
                                            <p class="text-gray-500 text-sm">الأيام المتبقية</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($searchResult->remainingDays); ?> يوم</p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($status['status'] === 'expired'): ?>
                            <div class="px-6 py-4 bg-red-600 text-white text-center text-lg font-bold">
                                هذه الرخصة منتهية
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="min-h-screen bg-gradient-to-r from-blue-500 to-purple-600 p-8">
                        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
                            <div class="p-8 text-center">
                                <p class="text-gray-600">لم يتم العثور على الرخصة. الرجاء التأكد من صحة رقم الرخصة.</p>
                                <p class="text-gray-500 mt-2">يوجد حالياً <?php echo $licenseCounts['total']; ?> رخصة في النظام (<?php echo $licenseCounts['active']; ?> سارية، <?php echo $licenseCounts['expired']; ?> منتهية)</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner = null;

        function startScanner() {
            const reader = document.getElementById('reader');
            reader.classList.toggle('hidden');

            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", 
                    { 
                        fps: 10,
                        qrbox: {width: 250, height: 250},
                        aspectRatio: 1.0
                    }
                );

                html5QrcodeScanner.render((decodedText) => {
                    const searchInput = document.querySelector('input[name="search"]');
                    searchInput.value = decodedText;
                    searchInput.form.submit();
                });
            }
        }

        // التحقق من دعم الجهاز للكاميرا
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const scanButton = document.querySelector('button[onclick="startScanner()"]');
            scanButton.style.display = 'none';
        }
    </script>
</body>
</html>
