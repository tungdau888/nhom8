<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->execute([$username, $email]);
    
    if ($stmt_check->rowCount() > 0) {
        echo "<script>alert('Username hoặc Email đã được sử dụng!'); window.history.back();</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$username, $hashed_password, $email])) {
            echo "<script>alert('Đăng ký thành công! Hãy bấm đăng nhập.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
        }
    }
}
?>