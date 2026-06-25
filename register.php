<?php
// register.php
session_start();
require_once 'config.php';

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp!";
        $msgType = "danger";
    } else {
        // Kiểm tra xem username hoặc email đã tồn tại chưa
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->execute([$username, $email]);
        
        if ($stmt_check->rowCount() > 0) {
            $message = "Tên đăng nhập hoặc Email đã có người sử dụng!";
            $msgType = "danger";
        } else {
            // Băm mật khẩu để bảo mật
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Mặc định tài khoản mới tạo sẽ có role là 'user'
            $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$username, $hashed_password, $email])) {
                $message = "Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...";
                $msgType = "success";
                // Tự động chuyển qua trang đăng nhập sau 2 giây
                header("refresh:2;url=login.php");
            } else {
                $message = "Đã xảy ra lỗi, vui lòng thử lại!";
                $msgType = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký - NCT Clone</title>
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
        <h3 class="text-center fw-bold mb-4" style="color: #00e6e6;">Đăng Ký</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-secondary">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn w-100 fw-bold" style="background-color: #00e6e6; color: #000;">Đăng Ký Tài Khoản</button>
            <div class="text-center mt-3 text-secondary">
                Đã có tài khoản? <a href="login.php" style="color: #00e6e6;">Đăng nhập</a>
            </div>
        </form>
    </div>
</body>
</html>