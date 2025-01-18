<?php
$servername = "localhost"; // یا IP سرور دیتابیس
$username = "root"; // نام کاربری دیتابیس
$password = ""; // رمز عبور دیتابیس
$dbname = "shahkar_api"; // نام دیتابیس

// ایجاد اتصال به دیتابیس
$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("اتصال به دیتابیس انجام نشد: " . $conn->connect_error);
}
?>
