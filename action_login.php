<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]); // Cho phép nhập cả username hoặc email
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Đăng nhập thành công -> Load lại trang chủ
        header("Location: index.php");
        exit;
    } else {
        // Báo lỗi (hiển thị popup JS và quay về)
        echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu!'); window.history.back();</script>";
    }
}
?>