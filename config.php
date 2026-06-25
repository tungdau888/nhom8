<?php
// config.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'nhaccuatui_clone');
define('DB_USER', 'root'); // Thay đổi nếu user của bạn khác
define('DB_PASS', '');     // Thay đổi nếu có mật khẩu

try {
    // Chuỗi kết nối DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    // Tùy chọn PDO: Cảnh báo lỗi, fetch dữ liệu dạng mảng liên hợp, vô hiệu hóa emulate prepares
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Khởi tạo kết nối
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Bắt lỗi nếu kết nối thất bại và trả về JSON báo lỗi (tránh lộ thông tin hệ thống)
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()
    ]);
    exit;
}
?>