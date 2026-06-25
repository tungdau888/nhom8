<?php
// api_get_songs.php

// 1. Cấp quyền CORS (rất quan trọng khi Frontend Fetch API từ domain/port khác)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
// 2. Thiết lập header trả về định dạng JSON
header('Content-Type: application/json; charset=utf-8');

// 3. Import file kết nối database
require_once 'config.php';

try {
    // 4. Viết câu lệnh SQL (LEFT JOIN để phòng trường hợp bài hát chưa có category_id)
    $sql = "SELECT 
                s.id, 
                s.title, 
                s.artist, 
                s.audio_url, 
                s.image_url, 
                s.views, 
                s.duration,
                s.created_at,
                c.name AS category_name 
            FROM songs s
            LEFT JOIN categories c ON s.category_id = c.id
            ORDER BY s.views DESC"; // Sắp xếp ưu tiên bài hát nhiều lượt xem nhất

    // 5. Chuẩn bị và thực thi truy vấn
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // 6. Lấy toàn bộ dữ liệu (đã được config fetch dưới dạng mảng liên hợp ở config.php)
    $songs = $stmt->fetchAll();

    // 7. Trả về kết quả JSON thành công
    echo json_encode([
        'status' => 'success',
        'count' => count($songs),
        'data' => $songs
    ], JSON_UNESCAPED_UNICODE); // JSON_UNESCAPED_UNICODE giúp hiển thị đúng tiếng Việt

} catch (PDOException $e) {
    // Trả về JSON lỗi nếu truy vấn thất bại
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn dữ liệu: ' . $e->getMessage()
    ]);
}
?>