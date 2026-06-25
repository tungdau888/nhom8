<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
    $albums = $pdo->query("SELECT * FROM albums ORDER BY id DESC")->fetchAll();
    $songs = $pdo->query("SELECT s.*, c.name as category_name, a.title as album_title FROM songs s LEFT JOIN categories c ON s.category_id = c.id LEFT JOIN albums a ON s.album_id = a.id ORDER BY s.views DESC")->fetchAll();
    $banners = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll();
    $artists = $pdo->query("SELECT * FROM artists ORDER BY id DESC")->fetchAll();

    // Tự động tạo bảng site_config nếu chưa tồn tại
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS site_config (
            cfg_key VARCHAR(100) PRIMARY KEY,
            cfg_value TEXT NOT NULL
        )");
    } catch (PDOException $e) { /* ignore */ }

    // Đọc cấu hình website
    $site_config = [];
    try {
        $cfg_rows = $pdo->query("SELECT cfg_key, cfg_value FROM site_config")->fetchAll();
        foreach ($cfg_rows as $r) {
            $site_config[$r['cfg_key']] = $r['cfg_value'];
        }
    } catch (PDOException $e) {
        $site_config = [];
    }

    echo json_encode([
        'status' => 'success',
        'categories' => $categories,
        'albums' => $albums,
        'songs' => $songs,
        'banners' => $banners,
        'artists' => $artists,
        'site_config' => $site_config // <-- Truyền cấu hình ra đây
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>