<?php
// login.php
session_start();
require_once 'config.php';

// Nếu đã đăng nhập rồi thì đá luôn về đúng trang
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Tìm user theo username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kiểm tra user có tồn tại và mật khẩu có khớp không
    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin vào Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Phân quyền chuyển hướng
        if ($user['role'] === 'admin') {
            header("Location: admin.php"); // Vào trang quản trị
        } else {
            header("Location: index.php"); // Vào trang chủ nghe nhạc
        }
        exit;
    } else {
        $message = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - NCT Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #141414; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; }
        .auth-card { background-color: #1e1e1e; padding: 40px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .form-control { background-color: #2a2a2a; border: none; color: white; }
        .form-control:focus { background-color: #333; color: white; box-shadow: none; border: 1px solid #00e6e6; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h3 class="text-center fw-bold mb-4" style="color: #00e6e6;">Đăng Nhập</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-secondary">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn w-100 fw-bold" style="background-color: #00e6e6; color: #000;">Đăng Nhập</button>
            <div class="text-center mt-3 text-secondary">
                Chưa có tài khoản? <a href="register.php" style="color: #00e6e6;">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</body>
</html>