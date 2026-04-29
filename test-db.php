<?php
$host = '127.0.0.1';
$db   = 'hr_castle_eg';
$user = 'castle_eg_usr';
$pass = 'Kimo31611#';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1 style='color:green;'>✅ ممتاز! الاتصال بقاعدة البيانات يعمل بنجاح.</h1>";
    echo "<p>هذا يعني أن المشكلة في 'كاش' لارافيل أو السيرفر.</p>";
} catch (PDOException $e) {
    echo "<h1 style='color:red;'>❌ فشل الاتصال!</h1>";
    echo "<p>هذا يعني أن الباسوورد أو اليوزر غير صحيحين في لوحة FastPanel، لارافيل بريء من هذا الخطأ.</p>";
    echo "<p><strong>السبب التقني:</strong> " . $e->getMessage() . "</p>";
}
?>