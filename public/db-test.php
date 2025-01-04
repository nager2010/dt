<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // محاولة الاتصال بقاعدة البيانات
    $db = new PDO(
        'mysql:host=localhost;dbname=d;charset=utf8mb4',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    echo "تم الاتصال بقاعدة البيانات بنجاح!<br>";

    // التحقق من جدول الرخص
    $stmt = $db->query("SHOW TABLES LIKE 'issuing_licenses'");
    if ($stmt->rowCount() > 0) {
        echo "جدول issuing_licenses موجود<br>";
        
        // عرض الصلاحيات
        $stmt = $db->query("SHOW GRANTS FOR CURRENT_USER()");
        echo "<h3>صلاحيات المستخدم:</h3>";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo htmlspecialchars($row[0]) . "<br>";
        }

        // عرض محتوى جدول الرخص
        $stmt = $db->query("SELECT * FROM issuing_licenses LIMIT 5");
        echo "<h3>محتوى جدول الرخص:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>رقم الرخصة</th><th>اسم المشروع</th><th>الحالة</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['licenseNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['projectName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // اختبار البحث عن رخصة محددة
        $testLicense = "LN-0PQWGEBG";
        $stmt = $db->prepare("SELECT * FROM issuing_licenses WHERE licenseNumber = ?");
        $stmt->execute([$testLicense]);
        echo "<h3>نتيجة البحث عن الرخصة {$testLicense}:</h3>";
        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "تم العثور على الرخصة:<br>";
            echo "رقم الرخصة: " . htmlspecialchars($result['licenseNumber']) . "<br>";
            echo "اسم المشروع: " . htmlspecialchars($result['projectName']) . "<br>";
            echo "الحالة: " . htmlspecialchars($result['status']) . "<br>";
        } else {
            echo "لم يتم العثور على الرخصة<br>";
            
            // عرض جميع أرقام الرخص للمقارنة
            $stmt = $db->query("SELECT licenseNumber FROM issuing_licenses");
            echo "<h4>جميع أرقام الرخص في النظام:</h4>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo htmlspecialchars($row['licenseNumber']) . "<br>";
            }
        }
    } else {
        echo "خطأ: جدول issuing_licenses غير موجود!";
    }

} catch (PDOException $e) {
    echo "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
}
